<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Add vocational_training_institutions.email_verified_at.
 *
 * verifyEmail() has always written the moment an institution confirmed its
 * address:
 *
 *     $institution->email_verified_at = new Time();
 *
 * but no such column exists. database/migrations/stakeholder_management_schema.sql
 * adds email_verified_at to the USERS table (line 15, under
 * USE cms_authentication_authorization) and gives the institutions table only
 * 'status'. CakePHP drops a property with no column behind it when it saves,
 * without complaining, so that timestamp has never once been stored - and the
 * block in Admin/LpkRegistration/index.ctp that shows a green check beside the
 * verification date can never render, because the value it tests is always null.
 *
 * The equivalent SQL, for anyone who would rather run it by hand:
 *
 *     ALTER TABLE vocational_training_institutions
 *         ADD COLUMN email_verified_at DATETIME NULL;
 *
 * No AFTER clause: MySQL takes one and SQLite does not, and the statement is
 * worth keeping runnable on both so this shell can be tested somewhere other
 * than production. The column lands at the end of the table, which nothing
 * here reads positionally.
 *
 * This shell is offered instead because it resolves the connection from the
 * application's own configuration - vocational_training_institutions lives on
 * cms_tmm_stakeholders, not on the default connection - so the ALTER cannot
 * land in the wrong database, and no password goes on the command line.
 *
 * Nothing is backfilled. The dates were never recorded anywhere, so there is
 * nothing to recover; existing rows keep a null until they verify again.
 *
 * Usage:
 *     bin/cake add_institution_email_verified_at            report only
 *     bin/cake add_institution_email_verified_at --apply    add the column
 */
class AddInstitutionEmailVerifiedAtShell extends Shell
{
    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription(
                'Add the email_verified_at column that verifyEmail() has been writing to nothing.'
            )
            ->addOption('apply', [
                'help' => 'Run the ALTER. Without it the column is only reported on.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');

        $table = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
        $connectionName = $table->getConnection()->configName();
        $connection = ConnectionManager::get($connectionName);

        $this->out(sprintf('Connection: <info>%s</info>', $connectionName));

        try {
            $columns = $connection->getSchemaCollection()
                ->describe('vocational_training_institutions')
                ->columns();
        } catch (\Exception $e) {
            $this->abort('Could not read the table: ' . $e->getMessage());
        }

        if (in_array('email_verified_at', $columns, true)) {
            $this->out('<success>Nothing to do: email_verified_at is already there.</success>');

            return null;
        }

        $sql = 'ALTER TABLE vocational_training_institutions '
            . 'ADD COLUMN email_verified_at DATETIME NULL';

        $this->out('The column is missing. This would run:');
        $this->out('');
        $this->out('  <info>' . $sql . '</info>');
        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to add the column.');

            return null;
        }

        try {
            $connection->execute($sql);
        } catch (\Exception $e) {
            $this->abort('The ALTER failed: ' . $e->getMessage());
        }

        // The ORM caches table descriptions; a stale one would keep dropping
        // the field on save even though the column now exists.
        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out('<success>Column added.</success>');
        $this->out('Clear the application cache too, so the web process picks up the new schema:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        return null;
    }
}
