<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Create apprentice_order_shares, the table behind sharing an order with LPKs.
 *
 * The email_templates table has carried apprentice_order_shared and
 * apprentice_order_cancelled for some time, both active, with nothing in the
 * application asking for either key - they were written for a feature that had
 * no code. This table is the missing half.
 *
 * It sits on cms_tmm_trainees, beside apprentice_orders, and is resolved from
 * the application's own configuration rather than named on a command line, so
 * the CREATE cannot land in the wrong database and no password is typed.
 *
 * The equivalent SQL, for anyone who would rather run it by hand:
 *
 *     CREATE TABLE apprentice_order_shares (
 *         id INT AUTO_INCREMENT PRIMARY KEY,
 *         apprentice_order_id INT NOT NULL,
 *         vocational_training_institution_id INT NOT NULL,
 *         lpk_name VARCHAR(255) NULL,
 *         lpk_email VARCHAR(255) NULL,
 *         status VARCHAR(20) NOT NULL DEFAULT 'shared',
 *         shared_by_user_id INT NULL,
 *         shared_by_name VARCHAR(255) NULL,
 *         notified TINYINT(1) NOT NULL DEFAULT 0,
 *         created DATETIME NULL,
 *         cancelled_at DATETIME NULL,
 *         UNIQUE KEY uq_order_lpk (apprentice_order_id, vocational_training_institution_id),
 *         KEY idx_order (apprentice_order_id)
 *     );
 *
 * No foreign key to vocational_training_institutions: that table is in another
 * database, and a constraint across schemas is not something to add quietly on
 * a running server. The institution's name and email are copied onto the row
 * when the order is shared, so a share stays readable whatever happens to the
 * institution afterwards.
 *
 * A copy of this table was found in cms_tmm_apprentices, beside an abandoned
 * apprentice_orders of an older design - different columns, four rows of
 * placeholder data, and nothing in the application reading either. Creating
 * the table in the right place while leaving that one standing would put the
 * same table name in two databases again, which is the very shape this was
 * meant to resolve. So the second step sets the stray copy aside by renaming
 * it rather than dropping it: a table that turns out to matter can be renamed
 * back, and one that is dropped cannot.
 *
 * Usage:
 *     bin/cake create_apprentice_order_shares            report only
 *     bin/cake create_apprentice_order_shares --apply    create it, set strays aside
 */
class CreateApprenticeOrderSharesShell extends Shell
{
    /** The table this shell is about. */
    const TABLE = 'apprentice_order_shares';

    /** What a copy in the wrong database is renamed to. */
    const SET_ASIDE = 'apprentice_order_shares_old';

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Create the apprentice_order_shares table, and set aside any stray copy.')
            ->addOption('apply', [
                'help' => 'Run the CREATE and the renames. Without it they are only reported on.',
                'boolean' => true,
            ])
            ->addOption('keep-strays', [
                'help' => 'Create the table but leave a copy in another database alone.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');

        $connectionName = TableRegistry::getTableLocator()
            ->get('ApprenticeOrders')->getConnection()->configName();
        $connection = ConnectionManager::get($connectionName);

        $this->out(sprintf('Connection: <info>%s</info>', $connectionName));

        $existing = $connection->getSchemaCollection()->listTables();
        $present = in_array(self::TABLE, $existing, true);

        if ($present) {
            $this->out(sprintf('<success>%s is already there.</success>', self::TABLE));
            // Not a return: a stray copy elsewhere still needs setting aside,
            // and that is the case this shell was extended to handle.
            $this->setAsideStrays($connectionName, $apply);

            return null;
        }

        $sqlite = strpos(strtolower(get_class($connection->getDriver())), 'sqlite') !== false;

        $sql = $sqlite
            ? 'CREATE TABLE apprentice_order_shares (
                   id INTEGER PRIMARY KEY AUTOINCREMENT,
                   apprentice_order_id INTEGER NOT NULL,
                   vocational_training_institution_id INTEGER NOT NULL,
                   lpk_name TEXT NULL,
                   lpk_email TEXT NULL,
                   status TEXT NOT NULL DEFAULT \'shared\',
                   shared_by_user_id INTEGER NULL,
                   shared_by_name TEXT NULL,
                   notified INTEGER NOT NULL DEFAULT 0,
                   created TEXT NULL,
                   cancelled_at TEXT NULL,
                   UNIQUE (apprentice_order_id, vocational_training_institution_id)
               )'
            : 'CREATE TABLE apprentice_order_shares (
                   id INT AUTO_INCREMENT PRIMARY KEY,
                   apprentice_order_id INT NOT NULL,
                   vocational_training_institution_id INT NOT NULL,
                   lpk_name VARCHAR(255) NULL,
                   lpk_email VARCHAR(255) NULL,
                   status VARCHAR(20) NOT NULL DEFAULT \'shared\',
                   shared_by_user_id INT NULL,
                   shared_by_name VARCHAR(255) NULL,
                   notified TINYINT(1) NOT NULL DEFAULT 0,
                   created DATETIME NULL,
                   cancelled_at DATETIME NULL,
                   UNIQUE KEY uq_order_lpk (apprentice_order_id, vocational_training_institution_id),
                   KEY idx_order (apprentice_order_id)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

        $this->out(sprintf('%s is missing here. This would run:', self::TABLE));
        $this->out('');
        $this->out('  <info>' . preg_replace('/\s+/', ' ', $sql) . '</info>');
        $this->out('');

        if (!$apply) {
            $this->setAsideStrays($connectionName, false);
            $this->out('');
            $this->out('<info>Nothing changed.</info> Run it again with --apply.');

            return null;
        }

        try {
            $connection->execute($sql);
        } catch (\Exception $e) {
            $this->abort('The CREATE failed: ' . $e->getMessage());
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out('<success>Table created.</success>');

        // Only now: a stray is set aside once the real table exists, never
        // before. Renaming first would leave a window with no table at all.
        $this->setAsideStrays($connectionName, $apply);

        $this->out('Clear the application cache too, so the web process sees it:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        return null;
    }

    /**
     * Rename any copy of the table living in another database.
     *
     * Renamed, not dropped. A table that turns out to matter can be renamed
     * back; one that is dropped is gone, and this copy holds rows somebody
     * once entered. The row count is printed for the same reason - setting
     * aside an empty table and setting aside a table with data in it are
     * different decisions, and the person running this should see which one
     * they are making.
     *
     * @param string $canonical The connection the table belongs on.
     * @param bool $apply Whether to run the renames.
     * @return void
     */
    protected function setAsideStrays($canonical, $apply)
    {
        if ($this->param('keep-strays')) {
            return;
        }

        // Skipping the canonical connection by name is not enough. Several
        // aliases point at one database here - 'default' and 'cms_masters'
        // deliberately so - and an alias for the canonical database would
        // find the table just created and rename it straight back out of
        // existence. So the canonical DATABASE is what gets skipped, and each
        // database is visited once however many names it answers to.
        $seen = [];
        try {
            $canonicalConfig = ConnectionManager::get($canonical)->config();
            $seen[isset($canonicalConfig['database']) ? $canonicalConfig['database'] : $canonical] = true;
        } catch (\Exception $e) {
            return;
        }

        $strays = [];
        foreach (ConnectionManager::configured() as $name) {
            if ($name === $canonical) {
                continue;
            }
            try {
                $connection = ConnectionManager::get($name);
                $config = $connection->config();
                $database = isset($config['database']) ? $config['database'] : $name;
                if (isset($seen[$database])) {
                    continue;
                }
                $seen[$database] = true;
                $tables = $connection->getSchemaCollection()->listTables();
            } catch (\Exception $e) {
                continue;
            }
            if (in_array(self::TABLE, $tables, true)) {
                $strays[$name] = [
                    'connection' => $connection,
                    'taken' => in_array(self::SET_ASIDE, $tables, true),
                ];
            }
        }

        if (!$strays) {
            return;
        }

        $this->out('');
        $this->out(sprintf('<warning>%s also exists in %d other database(s)</warning>',
            self::TABLE, count($strays)));

        foreach ($strays as $name => $stray) {
            $rows = '?';
            try {
                $rows = (int)$stray['connection']
                    ->execute('SELECT COUNT(*) FROM `' . self::TABLE . '`')->fetch()[0];
            } catch (\Exception $e) {
            }

            if ($stray['taken']) {
                $this->out(sprintf('  %-36s %s row(s) - <warning>%s already exists there, left alone</warning>',
                    $name, $rows, self::SET_ASIDE));
                continue;
            }

            if (!$apply) {
                $this->out(sprintf('  %-36s %s row(s) - would be renamed to %s',
                    $name, $rows, self::SET_ASIDE));
                continue;
            }

            $sqlite = strpos(strtolower(get_class($stray['connection']->getDriver())), 'sqlite') !== false;
            $sql = $sqlite
                ? 'ALTER TABLE `' . self::TABLE . '` RENAME TO `' . self::SET_ASIDE . '`'
                : 'RENAME TABLE `' . self::TABLE . '` TO `' . self::SET_ASIDE . '`';
            try {
                $stray['connection']->execute($sql);
                $this->out(sprintf('  %-36s %s row(s) - <success>renamed to %s</success>',
                    $name, $rows, self::SET_ASIDE));
            } catch (\Exception $e) {
                $this->out(sprintf('  %-36s <warning>rename failed: %s</warning>',
                    $name, $e->getMessage()));
            }
        }

        if (!$apply) {
            $this->out('');
            $this->out('  Nothing renamed. The rename happens with --apply, or never');
            $this->out('  with --keep-strays.');
        }
    }
}
