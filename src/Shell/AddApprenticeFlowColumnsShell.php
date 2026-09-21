<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Add the columns the apprentice hand-over needs.
 *
 * Two decisions at the end of the pipeline had nowhere to be recorded, and
 * the reports were counting both of them already.
 *
 * 1. Departing for Japan.
 *    apprentices.is_apprenticeship_pass is written as 0 by the promotion that
 *    creates the record, and nothing in the departure screens ever sets it -
 *    not the passport, not the visa, not the flight. The reports read it as
 *    "in Japan", so the number could only move if somebody edited the
 *    apprentice by hand and happened to know what that checkbox meant. The
 *    call belongs to tmm-training, who hears the test results summarised in
 *    the certificate and hears from tmm-documentation whether the departure
 *    documents and the medical check-up are in order.
 *
 * 2. Finishing the programme.
 *    apprentices.is_apprentice_pass is read in three places - twice in the
 *    reports as "completed", once to colour a row - and written in none. The
 *    completed count therefore stays at zero however many people finish. That
 *    call belongs to tmm-training as well.
 *
 * Both now get a timestamp and the user who made the call, so "who said this
 * person had gone" has an answer. The medical standing is stored the same way
 * it is for candidates, so tmm-training can see what tmm-documentation's
 * check-up came to without opening it.
 *
 * The equivalent SQL, for anyone who would rather run it by hand:
 *
 *     -- cms_tmm_apprentices
 *     ALTER TABLE apprentices ADD COLUMN is_apprentice_pass TINYINT(1) NULL;
 *     ALTER TABLE apprentices ADD COLUMN departed_at DATETIME NULL;
 *     ALTER TABLE apprentices ADD COLUMN departed_by INT NULL;
 *     ALTER TABLE apprentices ADD COLUMN completed_at DATETIME NULL;
 *     ALTER TABLE apprentices ADD COLUMN completed_by INT NULL;
 *     ALTER TABLE apprentices ADD COLUMN mcu_result VARCHAR(10) NULL;
 *     ALTER TABLE apprentices ADD COLUMN mcu_checked_at DATETIME NULL;
 *
 * No AFTER clause: MySQL takes one and SQLite does not, and these statements
 * are worth keeping runnable on both so the shell can be tested somewhere
 * other than production.
 *
 * is_apprentice_pass is listed here because the reports read it; on an
 * installation that already has the column the shell says so and adds
 * nothing.
 *
 * Usage:
 *     bin/cake add_apprentice_flow_columns            report only
 *     bin/cake add_apprentice_flow_columns --apply    add the columns
 */
class AddApprenticeFlowColumnsShell extends Shell
{
    /**
     * Table => [column => definition], resolved to a connection through the
     * application's own configuration so an ALTER cannot land in the wrong
     * database and no password goes on the command line.
     *
     * @var array
     */
    const WANTED = [
        'Apprentices' => [
            'table' => 'apprentices',
            'columns' => [
                'is_apprentice_pass' => 'TINYINT(1) NULL',
                'departed_at' => 'DATETIME NULL',
                'departed_by' => 'INT NULL',
                'completed_at' => 'DATETIME NULL',
                'completed_by' => 'INT NULL',
                'mcu_result' => 'VARCHAR(10) NULL',
                'mcu_checked_at' => 'DATETIME NULL',
            ],
        ],
    ];

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Add the columns the departure and completion calls need.')
            ->addOption('apply', [
                'help' => 'Run the ALTERs. Without it the columns are only reported on.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');
        $statements = [];

        foreach (self::WANTED as $alias => $spec) {
            $this->out('');
            $this->out(sprintf('<info>%s</info> (%s)', $spec['table'], $alias));

            try {
                $table = TableRegistry::getTableLocator()->get($alias);
                $connectionName = $table->getConnection()->configName();
                $existing = ConnectionManager::get($connectionName)
                    ->getSchemaCollection()
                    ->describe($spec['table'])
                    ->columns();
            } catch (\Exception $e) {
                $this->err('  <error>Could not read the table:</error> ' . $e->getMessage());
                continue;
            }

            $this->out(sprintf('  connection: %s', $connectionName));

            foreach ($spec['columns'] as $column => $definition) {
                if (in_array($column, $existing, true)) {
                    $this->out(sprintf('  <success>already there</success>  %s', $column));
                    continue;
                }
                $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s', $spec['table'], $column, $definition);
                $this->out(sprintf('  <warning>missing</warning>      %s', $column));
                $statements[] = [$connectionName, $sql];
            }
        }

        $this->out('');

        if (!$statements) {
            $this->out('<success>Nothing to do: every column is already there.</success>');
            $this->_reportStanding();

            return null;
        }

        $this->out('This would run:');
        foreach ($statements as list($connectionName, $sql)) {
            $this->out(sprintf('  [%s] <info>%s</info>', $connectionName, $sql));
        }
        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to add the columns.');

            return null;
        }

        foreach ($statements as list($connectionName, $sql)) {
            try {
                ConnectionManager::get($connectionName)->execute($sql);
                $this->out(sprintf('  <success>done</success>  %s', $sql));
            } catch (\Exception $e) {
                $this->err(sprintf('  <error>failed</error>  %s', $sql));
                $this->abort('  ' . $e->getMessage());
            }
        }

        // The ORM caches table descriptions; a stale one keeps dropping the new
        // fields on save even though the columns now exist.
        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out('');
        $this->out('<success>Columns added.</success>');
        $this->out('Clear the application cache too, so the web process sees the new schema:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        $this->_reportStanding();

        return null;
    }

    /**
     * Where the apprentices stand now, so it is clear what the new screens
     * will show on the first visit.
     *
     * @return void
     */
    protected function _reportStanding()
    {
        $this->out('');
        $this->out('<info>Apprentices</info>');

        try {
            $apprentices = TableRegistry::getTableLocator()->get('Apprentices');
            $schema = $apprentices->getSchema();
            $total = $apprentices->find()->count();
        } catch (\Exception $e) {
            $this->err('  could not be read: ' . $e->getMessage());

            return;
        }

        $this->out(sprintf('  %-34s %d', 'on file', $total));

        foreach ([
            'is_apprenticeship_pass' => 'recorded as departed for Japan',
            'is_apprentice_pass' => 'recorded as having completed',
        ] as $column => $label) {
            if (!$schema->hasColumn($column)) {
                $this->out(sprintf('  %-34s <warning>column missing</warning>', $label));
                continue;
            }
            $count = $apprentices->find()->where([$column => 1])->count();
            $this->out(sprintf('  %-34s %d', $label, $count));
        }

        $this->out('');
        $this->out('tmm-training makes both calls, from <info>/apprentices/departure-readiness</info>.');
        $this->out('The screen shows the certificate, the document count and the medical');
        $this->out('standing beside each apprentice; it does not decide for anybody.');
        $this->out('A result marked not fit at <info>/master-medical-check-up-results</info> is the');
        $this->out('one thing that refuses a departure outright.');
    }
}
