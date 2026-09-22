<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Prepare the table the decision trail is written to.
 *
 * The application has an Audit module and a `logs` table behind it, and no
 * code anywhere writes to either. The module is a per-role feature guide, not
 * a trail, so the honest answer to "who decided this" has until now been
 * nowhere. Worse, the table is not in any schema export in this repository,
 * and those exports are behind the live database - so whether it exists at
 * all can only be answered against the real server.
 *
 * This reports what is there before changing anything: whether the table
 * exists, which of the things the trail records already have a column, and
 * which do not. With --apply it creates the table, or adds only what is
 * absent, and never drops or retypes a column that is already there.
 *
 * It does not insist on its own names. The `logs` table on a live
 * installation turned out to predate the trail and to have names for most of
 * what it needs - the subject is `model` plus `foreign_key`, the free text is
 * `description`, the address is `ip_address`. Adding subject_type,
 * subject_id, detail and ip beside them would have put two names on one
 * meaning in a single table. AuditTable::COLUMN_CANDIDATES lists the
 * acceptable names for each thing and this shell honours it, so only what is
 * genuinely missing gets added.
 *
 * Why these things are recorded at all. A trail that stores only ids answers
 * nothing once a user or a candidate is deleted, and deletion is exactly when
 * somebody starts asking. So the username, the role names and a label for the
 * subject are captured as text at the moment of the decision. They are a
 * photograph, not a reference, and they stay readable when the row they
 * describe is gone.
 *
 * Usage:
 *     bin/cake add_audit_log             report only
 *     bin/cake add_audit_log --apply     create or extend the table
 */
class AddAuditLogShell extends Shell
{
    const AUTH_DB = 'cms_authentication_authorization';
    const TABLE = 'logs';

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Create or extend the table the decision trail is written to.')
            ->addOption('apply', [
                'help' => 'Create the table or add the missing columns. Without it nothing changes.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');

        try {
            $connection = ConnectionManager::get(self::AUTH_DB);
            $tables = $connection->getSchemaCollection()->listTables();
        } catch (\Exception $e) {
            $this->abort('Could not read the database: ' . $e->getMessage());
        }

        $this->out('');
        $this->out(sprintf('<info>%s</info> on %s', self::TABLE, self::AUTH_DB));

        $exists = in_array(self::TABLE, $tables, true);

        if (!$exists) {
            $this->out('  <warning>the table does not exist</warning>');
            $sql = $this->_createSql();
            $this->out('');
            $this->out('This would run:');
            foreach (explode("\n", $sql) as $line) {
                $this->out('  <info>' . $line . '</info>');
            }

            if (!$apply) {
                $this->out('');
                $this->out('<info>Nothing changed.</info> Run it again with --apply to create it.');

                return null;
            }

            try {
                $connection->execute($sql);
            } catch (\Exception $e) {
                $this->abort('Could not create the table: ' . $e->getMessage());
            }
            $this->out('');
            $this->out('<success>Table created.</success>');
            $this->_afterword();

            return null;
        }

        $this->out('  <success>the table exists</success>');

        try {
            $existing = $connection->getSchemaCollection()->describe(self::TABLE)->columns();
        } catch (\Exception $e) {
            $this->abort('Could not describe the table: ' . $e->getMessage());
        }

        $logs = \Cake\ORM\TableRegistry::getTableLocator()->get('Audit');
        $map = $logs->columnMap($existing);

        $statements = [];
        foreach (\App\Model\Table\AuditTable::COLUMN_CANDIDATES as $canonical => $candidates) {
            if (isset($map[$canonical])) {
                $note = $map[$canonical] === $canonical
                    ? ''
                    : sprintf(' (as %s)', $map[$canonical]);
                $this->out(sprintf('  <success>already there</success>  %s%s', $canonical, $note));
                continue;
            }
            // Nothing on the table means this thing, under any of its names.
            $column = $candidates[0];
            $definition = \App\Model\Table\AuditTable::COLUMN_TYPES[$canonical];
            $this->out(sprintf('  <warning>missing</warning>      %s', $column));
            $statements[] = sprintf('ALTER TABLE %s ADD COLUMN %s %s', self::TABLE, $column, $definition);
        }

        $spare = array_diff($existing, array_values($map), ['id']);
        if ($spare) {
            $this->out('');
            $this->out('  columns already on the table that the trail does not use, left alone:');
            $this->out('    ' . implode(', ', $spare));
        }

        $this->out('');

        if (!$statements) {
            $this->out('<success>Nothing to do: every column the trail needs is there.</success>');
            $this->_afterword();

            return null;
        }

        $this->out('This would run:');
        foreach ($statements as $sql) {
            $this->out('  <info>' . $sql . '</info>');
        }
        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to add the columns.');

            return null;
        }

        foreach ($statements as $sql) {
            try {
                $connection->execute($sql);
                $this->out(sprintf('  <success>done</success>  %s', $sql));
            } catch (\Exception $e) {
                $this->err(sprintf('  <error>failed</error>  %s', $sql));
                $this->abort('  ' . $e->getMessage());
            }
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out('');
        $this->out('<success>Columns added.</success>');
        $this->_afterword();

        return null;
    }

    /**
     * @return string
     */
    protected function _createSql()
    {
        // The first name in each candidate list is the one a fresh table gets,
        // so a new installation ends up with the same shape as the one already
        // deployed rather than a second vocabulary.
        $lines = [];
        $created = null;
        $subjectType = null;
        $subjectId = null;
        foreach (\App\Model\Table\AuditTable::COLUMN_CANDIDATES as $canonical => $candidates) {
            $column = $candidates[0];
            $lines[] = sprintf('  %s %s,', $column, \App\Model\Table\AuditTable::COLUMN_TYPES[$canonical]);
            if ($canonical === 'created') {
                $created = $column;
            }
            if ($canonical === 'subject_type') {
                $subjectType = $column;
            }
            if ($canonical === 'subject_id') {
                $subjectId = $column;
            }
        }

        return sprintf(
            "CREATE TABLE %s (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n%s\n  KEY idx_logs_created (%s),\n  KEY idx_logs_subject (%s, %s)\n)",
            self::TABLE,
            implode("\n", $lines),
            $created,
            $subjectType,
            $subjectId
        );
    }

    /**
     * @return void
     */
    protected function _afterword()
    {
        $this->out('');
        $this->out('Clear the application cache so the web process sees the schema:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');
        $this->out('');
        $this->out('From then on the twelve decisions in the pipeline write a row each:');
        $this->out('  putting a candidate forward and taking it back, promoting to');
        $this->out('  trainee and to apprentice, recording a departure and a completed');
        $this->out('  programme and taking either back, setting an owing cost, and the');
        $this->out('  three permission changes.');
        $this->out('Read them at <info>/audit/trail</info>.');
    }
}
