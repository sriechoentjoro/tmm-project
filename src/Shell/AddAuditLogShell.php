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
 * exists, which of the columns the trail needs are present, and which are
 * missing. With --apply it creates the table, or adds only the columns that
 * are absent, and never drops or retypes one that is already there.
 *
 * Why these columns. A trail that stores only ids answers nothing once a user
 * or a candidate is deleted, and deletion is exactly when somebody starts
 * asking. So the username, the role names and a label for the subject are
 * captured as text at the moment of the decision. They are a photograph, not
 * a reference, and they stay readable when the row they describe is gone.
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
     * column => definition. Order is the order they are added in.
     *
     * @var array
     */
    const COLUMNS = [
        'user_id' => 'INT NULL',
        'username' => 'VARCHAR(100) NULL',
        'role_names' => 'VARCHAR(255) NULL',
        'action' => 'VARCHAR(100) NULL',
        'subject_type' => 'VARCHAR(100) NULL',
        'subject_id' => 'INT NULL',
        'subject_label' => 'VARCHAR(255) NULL',
        'detail' => 'TEXT NULL',
        'ip' => 'VARCHAR(45) NULL',
        'created' => 'DATETIME NULL',
    ];

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

        $statements = [];
        foreach (self::COLUMNS as $column => $definition) {
            if (in_array($column, $existing, true)) {
                $this->out(sprintf('  <success>already there</success>  %s', $column));
                continue;
            }
            $this->out(sprintf('  <warning>missing</warning>      %s', $column));
            $statements[] = sprintf('ALTER TABLE %s ADD COLUMN %s %s', self::TABLE, $column, $definition);
        }

        $spare = array_diff($existing, array_keys(self::COLUMNS), ['id']);
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
        $lines = [];
        foreach (self::COLUMNS as $column => $definition) {
            $lines[] = sprintf('  %s %s,', $column, $definition);
        }

        return sprintf(
            "CREATE TABLE %s (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n%s\n  KEY idx_logs_created (created),\n  KEY idx_logs_subject (subject_type, subject_id)\n)",
            self::TABLE,
            implode("\n", $lines)
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
