<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Add the columns the selection hand-over needs.
 *
 * Two decisions in the recruitment process had nowhere to be recorded.
 *
 * 1. The institution declaring a candidate through selection.
 *    promoteToTrainee() listed only candidates whose is_candidate_pass was
 *    already 1, and doPromote() is the only thing in the application that sets
 *    that flag - at the same moment it creates the trainee, which then excludes
 *    the candidate from the list. So the list could only ever be empty. The
 *    missing step is the institution's own: it says the candidate has passed
 *    its selection, and that is what puts them in front of recruitment.
 *    is_candidate_pass stays what recruitment sets by promoting; it is the
 *    outcome, not the entry ticket.
 *
 * 2. Whether the medical check-up passed.
 *    master_medical_check_up_results holds nothing but an id and a title, so no
 *    code could tell "Fit" from "Unfit" - the promotion screen read
 *    candidates.mcu_score, which nothing writes, and showed a blank column.
 *    A fitness flag on the result type lets an administrator say once what each
 *    result means, and every check-up recorded against it inherits that meaning.
 *
 * The equivalent SQL, for anyone who would rather run it by hand:
 *
 *     -- cms_lpk_candidates
 *     ALTER TABLE candidates ADD COLUMN lpk_proposed_at DATETIME NULL;
 *     ALTER TABLE candidates ADD COLUMN lpk_proposed_by INT NULL;
 *     ALTER TABLE candidates ADD COLUMN mcu_result VARCHAR(10) NULL;
 *     ALTER TABLE candidates ADD COLUMN mcu_checked_at DATETIME NULL;
 *
 *     -- cms_masters
 *     ALTER TABLE master_medical_check_up_results ADD COLUMN is_fit TINYINT(1) NULL;
 *
 * No AFTER clause: MySQL takes one and SQLite does not, and these statements
 * are worth keeping runnable on both so the shell can be tested somewhere
 * other than production.
 *
 * is_fit is deliberately left NULL on every existing row. NULL means "nobody
 * has said", and the application treats that as unknown: it blocks nothing and
 * fails nobody. Marking the results that mean unfit is a decision for an
 * administrator, made on the master screen, and this shell prints the rows at
 * the end so it is clear which ones are waiting.
 *
 * Usage:
 *     bin/cake add_selection_flow_columns            report only
 *     bin/cake add_selection_flow_columns --apply    add the columns
 */
class AddSelectionFlowColumnsShell extends Shell
{
    /**
     * Table => [column => definition], resolved to a connection through the
     * application's own configuration so an ALTER cannot land in the wrong
     * database and no password goes on the command line.
     *
     * @var array
     */
    const WANTED = [
        'Candidates' => [
            'table' => 'candidates',
            'columns' => [
                'lpk_proposed_at' => 'DATETIME NULL',
                'lpk_proposed_by' => 'INT NULL',
                'mcu_result' => 'VARCHAR(10) NULL',
                'mcu_checked_at' => 'DATETIME NULL',
            ],
        ],
        'MasterMedicalCheckUpResults' => [
            'table' => 'master_medical_check_up_results',
            'columns' => [
                'is_fit' => 'TINYINT(1) NULL',
            ],
        ],
    ];

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Add the columns the LPK proposal and the MCU pass/fail need.')
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
            $this->_reportResults();

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

        $this->_reportResults();

        return null;
    }

    /**
     * Show the MCU result types and whether anyone has said what they mean.
     *
     * Until a result is marked unfit it blocks nothing, so this list is the
     * difference between the feature working and the feature being installed.
     *
     * @return void
     */
    protected function _reportResults()
    {
        $this->out('');
        $this->out('<info>MCU result types</info>');

        try {
            $rows = TableRegistry::getTableLocator()->get('MasterMedicalCheckUpResults')
                ->find()
                ->enableHydration(false)
                ->toArray();
        } catch (\Exception $e) {
            $this->err('  could not be read: ' . $e->getMessage());

            return;
        }

        if (!$rows) {
            $this->out('  (none on file)');

            return;
        }

        foreach ($rows as $row) {
            $isFit = array_key_exists('is_fit', $row) ? $row['is_fit'] : null;
            if ($isFit === null) {
                $meaning = '<warning>not said yet - blocks nobody</warning>';
            } elseif ((int)$isFit === 1) {
                $meaning = '<success>fit</success>';
            } else {
                $meaning = '<error>not fit - stops promotion</error>';
            }
            $this->out(sprintf('  #%-4s %-40s %s', $row['id'], $row['title'], $meaning));
        }

        $this->out('');
        $this->out('Set these on the master screen: <info>/master-medical-check-up-results</info>');
        $this->out('A result nobody has marked counts as unknown and keeps nobody out.');
    }
}
