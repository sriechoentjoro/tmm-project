<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Rename a table out of the way, without dropping it.
 *
 * check-duplicate-tables finds the same table name in two databases, and
 * compare_duplicate_table settles which copy is real. What is left is putting
 * the other one aside - and the safe way to do that is a rename, not a DROP.
 * A table that turns out to matter can be renamed back; one that is dropped
 * is gone, and these copies hold rows somebody once entered on purpose.
 *
 * Before renaming anything it checks the two ways a table can still be in use,
 * because getting this wrong takes a working screen offline:
 *
 *   - a Table class pointing at this very connection, which means the ORM
 *     reads this copy;
 *   - a source file naming the table, which means raw SQL may open it. This
 *     check exists because journal_details has no Table class at all and is
 *     queried directly by two controllers, holding live accounting rows.
 *
 * Either one stops the rename unless --force is given.
 *
 * Report only unless --apply.
 *
 * The scan reads code only: comments are stripped before searching, because a
 * table named in a docblock is being explained, not queried. Without that, this
 * shell objected to setting aside the very table its own usage example named,
 * and a comment elsewhere explaining a past bug blocked it too. Comments in
 * this codebase are deliberately concrete, and they should not have to be
 * vague to keep a tool honest.
 *
 * Usage:
 *     bin/cake set_aside_table --table=promotion_histories \
 *         --connection=cms_tmm_apprentices
 *     bin/cake set_aside_table --table=promotion_histories \
 *         --connection=cms_tmm_apprentices --apply
 */
class SetAsideTableShell extends Shell
{
    /** Appended to the table's name. */
    const SUFFIX = '_old';

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Rename a table out of the way, keeping its rows.')
            ->addOption('table', ['help' => 'Table to set aside.'])
            ->addOption('connection', ['help' => 'Connection the table sits on.'])
            ->addOption('apply', [
                'help' => 'Run the rename. Without it nothing changes.',
                'boolean' => true,
            ])
            ->addOption('force', [
                'help' => 'Rename even though something appears to still use it.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $table = trim((string)$this->param('table'));
        $connectionName = trim((string)$this->param('connection'));
        $apply = (bool)$this->param('apply');

        if ($table === '' || $connectionName === '') {
            $this->abort('Both --table and --connection are required.');
        }
        if (!preg_match('/^[a-z0-9_]+$/i', $table)) {
            // The name goes into SQL that cannot be parameterised.
            $this->abort('That does not look like a table name.');
        }

        try {
            $connection = ConnectionManager::get($connectionName);
            $tables = $connection->getSchemaCollection()->listTables();
        } catch (\Exception $e) {
            $this->abort('Could not reach ' . $connectionName . ': ' . $e->getMessage());
        }

        $target = $table . self::SUFFIX;

        if (!in_array($table, $tables, true)) {
            $this->out(sprintf('<success>Nothing to do: %s is not in %s.</success>',
                $table, $connectionName));

            return null;
        }
        if (in_array($target, $tables, true)) {
            $this->abort(sprintf('%s already exists in %s. Deal with that one first - '
                . 'this tool will not write over it.', $target, $connectionName));
        }

        $rows = '?';
        try {
            $rows = (int)$connection->execute('SELECT COUNT(*) FROM `' . $table . '`')->fetch()[0];
        } catch (\Exception $e) {
        }

        $this->out('');
        $this->out(sprintf('<info>%s</info> in <info>%s</info> holds %s row(s)',
            $table, $connectionName, $rows));
        $this->out(sprintf('would become <info>%s</info>', $target));
        $this->out('');

        foreach ($this->notes($table, $connectionName) as $note) {
            $this->out('  ' . $note);
        }

        $objections = $this->objections($table, $connectionName);
        foreach ($objections as $objection) {
            $this->out('  <warning>' . $objection . '</warning>');
        }
        if (!$objections) {
            $this->out('  nothing in the application appears to read this copy');
        }
        $this->out('');

        if ($objections && !$this->param('force')) {
            $this->out('<warning>Not renaming.</warning> Something still appears to use it.');
            $this->out('Check the objection above; --force overrides it if you are sure.');

            return null;
        }

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply.');
            $this->out('The rows are kept - the table is renamed, never dropped.');

            return null;
        }

        $sqlite = strpos(strtolower(get_class($connection->getDriver())), 'sqlite') !== false;
        $sql = $sqlite
            ? 'ALTER TABLE `' . $table . '` RENAME TO `' . $target . '`'
            : 'RENAME TABLE `' . $table . '` TO `' . $target . '`';

        try {
            $connection->execute($sql);
        } catch (\Exception $e) {
            $this->abort('The rename failed: ' . $e->getMessage());
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out(sprintf('<success>Renamed to %s, with its %s row(s).</success>', $target, $rows));
        $this->out('To put it back:');
        $this->out(sprintf('  <info>RENAME TABLE `%s` TO `%s`;</info>', $target, $table));
        $this->out('Clear the application cache so the web process sees it:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        return null;
    }

    /**
     * What is worth knowing but is not a reason to stop.
     *
     * A Table class reading some OTHER connection is the useful case: it says
     * the ORM is not looking at this copy, which is most of the question. The
     * source scan below cannot tell which connection a raw query opens, so
     * without this note its objection reads as though the whole feature might
     * break, when the ORM path has already been accounted for.
     *
     * @return array
     */
    protected function notes($table, $connectionName)
    {
        $alias = \Cake\Utility\Inflector::camelize($table);
        if (!is_file(APP . 'Model' . DS . 'Table' . DS . $alias . 'Table.php')) {
            return [];
        }

        try {
            $reads = \Cake\ORM\TableRegistry::getTableLocator()
                ->get($alias)->getConnection()->configName();
        } catch (\Exception $e) {
            return [];
        }

        if ($reads === $connectionName) {
            return []; // an objection says this, and says it louder
        }

        return [sprintf('%sTable reads %s, not this one - the ORM is not using this copy',
            $alias, $reads)];
    }

    /**
     * Reasons not to rename this table: the ways it might still be read.
     *
     * @return array Human-readable objections, empty when there are none.
     */
    protected function objections($table, $connectionName)
    {
        $objections = [];

        $alias = \Cake\Utility\Inflector::camelize($table);
        if (is_file(APP . 'Model' . DS . 'Table' . DS . $alias . 'Table.php')) {
            try {
                $reads = \Cake\ORM\TableRegistry::getTableLocator()
                    ->get($alias)->getConnection()->configName();
                if ($reads === $connectionName) {
                    $objections[] = sprintf(
                        '%sTable reads %s - this is the copy the application uses',
                        $alias, $connectionName);
                }
            } catch (\Exception $e) {
                $objections[] = $alias . 'Table exists but could not be loaded, so what it '
                    . 'reads is unknown';
            }
        }

        $files = $this->mentions($table);
        if ($files) {
            $objections[] = sprintf(
                '%d source file(s) name this table, so raw SQL may open it: %s',
                count($files), implode(', ', array_slice($files, 0, 4))
                    . (count($files) > 4 ? ', ...' : ''));
        }

        return $objections;
    }

    /**
     * @return array Paths, relative to src/, naming this table.
     */
    protected function mentions($table)
    {
        // The table's own Table class always names it, in setTable(). That
        // tells us nothing the connection check above has not already settled,
        // and listing it makes every table with a model look like it is in use
        // - which is the shape of objection people stop reading.
        $own = APP . 'Model' . DS . 'Table' . DS
            . \Cake\Utility\Inflector::camelize($table) . 'Table.php';

        $found = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(APP, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (!$file->isFile()
                || !in_array(strtolower($file->getExtension()), ['php', 'ctp'], true)) {
                continue;
            }
            if ($file->getPathname() === $own) {
                continue;
            }
            // Whole word: without it, setting aside promotion_histories would
            // be blocked by a reference to promotion_histories_old, which is a
            // different table. An underscore counts as a word character, so
            // the boundary falls exactly where the name ends.
            if (preg_match('/\b' . preg_quote($table, '/') . '\b/',
                $this->code($file->getPathname()))) {
                $found[] = str_replace(APP, '', $file->getPathname());
            }
        }

        return $found;
    }

    /**
     * A source file with its comments removed.
     *
     * Inline HTML in a template is kept: it is not code, but a table name
     * sitting in markup is odd enough to be worth surfacing. Comments are not
     * - explaining a table is not using it.
     *
     * @param string $path Absolute path.
     * @return string
     */
    protected function code($path)
    {
        $source = (string)file_get_contents($path);
        try {
            $tokens = token_get_all($source);
        } catch (\Throwable $e) {
            return $source; // unparseable: better to over-report than to miss
        }

        $code = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                    continue;
                }
                $code .= $token[1];
                continue;
            }
            $code .= $token;
        }

        return $code;
    }
}
