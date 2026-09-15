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
 * Usage:
 *     bin/cake create_apprentice_order_shares            report only
 *     bin/cake create_apprentice_order_shares --apply    create the table
 */
class CreateApprenticeOrderSharesShell extends Shell
{
    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Create the apprentice_order_shares table.')
            ->addOption('apply', [
                'help' => 'Run the CREATE. Without it the table is only reported on.',
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
        if (in_array('apprentice_order_shares', $existing, true)) {
            $this->out('<success>Nothing to do: apprentice_order_shares is already there.</success>');

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

        $this->out('The table is missing. This would run:');
        $this->out('');
        $this->out('  <info>' . preg_replace('/\s+/', ' ', $sql) . '</info>');
        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to create the table.');

            return null;
        }

        try {
            $connection->execute($sql);
        } catch (\Exception $e) {
            $this->abort('The CREATE failed: ' . $e->getMessage());
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out('<success>Table created.</success>');
        $this->out('Clear the application cache too, so the web process sees it:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        return null;
    }
}
