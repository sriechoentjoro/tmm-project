<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Say which medical check-up results mean fit, and recalculate what follows.
 *
 * master_medical_check_up_results carries an is_fit column, added when the
 * candidate selection flow was built, and both the candidate and the
 * apprentice check-ups read the same list - there is one marking, not two.
 * Until a row is marked it counts as "nobody has said", which blocks nobody
 * and fails nobody. That is the safe default, and it is also why the feature
 * sat installed but inert: three result types, none of them marked.
 *
 * Marking them by hand is three checkboxes on /master-medical-check-up-results.
 * This does the same thing from the title, so it can be run once and be right
 * on whatever rows an installation actually has:
 *
 *     "Tidak Fit", "Unfit", "Not fit", "Gagal"        -> not fit
 *     "Fit", "Fit dengan keterangan", "Lolos", "Pass" -> fit
 *     anything the rule does not recognise            -> left alone
 *
 * The negative words are tested first on purpose: "Tidak Fit" contains the
 * word "fit", so a rule that looked for "fit" first would mark it as fit and
 * quietly let unfit people through - the exact failure this column exists to
 * prevent.
 *
 * A row an administrator has already marked is never overwritten unless
 * --overwrite says so: a human decision outranks a guess from a title.
 *
 * Marking alone is not enough. candidates.mcu_result and
 * apprentices.mcu_result were worked out while every result was unknown, so
 * they all say "nobody has said". Applying the marking therefore recalculates
 * every candidate and every apprentice, and reports what moved.
 *
 * Usage:
 *     bin/cake mark_mcu_fitness              report only
 *     bin/cake mark_mcu_fitness --apply      write the markings and recalculate
 *     bin/cake mark_mcu_fitness --apply --overwrite   also re-mark rows a
 *                                            person had already marked
 */
class MarkMcuFitnessShell extends Shell
{
    /**
     * Titles that mean the person is not fit. Tested before the fit words,
     * because "Tidak Fit" contains "fit".
     *
     * @var array
     */
    const NOT_FIT = ['tidak fit', 'tidak lolos', 'tidak sehat', 'unfit', 'not fit', 'no fit', 'fail', 'gagal', 'ditolak'];

    /**
     * Titles that mean the person is fit.
     *
     * @var array
     */
    const FIT = ['fit', 'lolos', 'lulus', 'sehat', 'pass', 'layak', 'memenuhi'];

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Mark which MCU results mean fit, then recalculate every standing.')
            ->addOption('apply', [
                'help' => 'Write the markings and recalculate. Without it nothing changes.',
                'boolean' => true,
            ])
            ->addOption('overwrite', [
                'help' => 'Also re-mark rows somebody has already marked by hand.',
                'boolean' => true,
            ]);
    }

    /**
     * What the rule makes of one title.
     *
     * @param string $title The result title.
     * @return int|null 1 fit, 0 not fit, null when the rule does not recognise it.
     */
    public function readTitle($title)
    {
        $haystack = strtolower(trim((string)$title));
        if ($haystack === '') {
            return null;
        }

        foreach (self::NOT_FIT as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return 0;
            }
        }
        foreach (self::FIT as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return 1;
            }
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');
        $overwrite = (bool)$this->param('overwrite');

        try {
            $results = TableRegistry::getTableLocator()->get('MasterMedicalCheckUpResults');
            if (!$results->getSchema()->hasColumn('is_fit')) {
                $this->err('<error>master_medical_check_up_results has no is_fit column.</error>');
                $this->abort('Run bin/cake add_selection_flow_columns --apply first.');
            }
            $rows = $results->find()->order(['id' => 'ASC'])->toArray();
        } catch (\Exception $e) {
            $this->abort('Could not read the result types: ' . $e->getMessage());
        }

        if (!$rows) {
            $this->out('<warning>No result types on file.</warning> Nothing to mark.');

            return null;
        }

        $this->out('');
        $this->out('<info>MCU result types</info>');
        $this->out(sprintf('  %-4s %-36s %-18s %s', '#', 'title', 'marked now', 'the rule says'));

        $toWrite = [];
        foreach ($rows as $row) {
            $current = $row->get('is_fit');
            $proposed = $this->readTitle($row->get('title'));

            $currentLabel = $this->_label($current);
            if ($proposed === null) {
                $verdict = '<warning>does not recognise the title</warning>';
            } elseif ($current !== null && (int)$current === $proposed) {
                $verdict = 'already says ' . $this->_label($proposed);
            } elseif ($current !== null && !$overwrite) {
                $verdict = sprintf('<warning>would say %s, but a person said %s - kept</warning>',
                    $this->_label($proposed), $this->_label($current));
            } else {
                $verdict = '<success>' . $this->_label($proposed) . '</success>';
                $toWrite[] = [$row, $proposed];
            }

            $this->out(sprintf('  #%-3s %-36s %-18s %s', $row->id, $row->get('title'), $currentLabel, $verdict));
        }

        $this->out('');

        if (!$toWrite) {
            $this->out('<success>Nothing to mark: every row already says what the rule would say,</success>');
            $this->out('or is one the rule does not recognise and leaves alone.');
            if ($apply) {
                $this->_recalculate();
            }

            return null;
        }

        if (!$apply) {
            $this->out(sprintf('<info>%d row(s) would be marked.</info> Run it again with --apply to write them.', count($toWrite)));
            $this->out('A row nobody has marked blocks nobody, so nothing is enforced until then.');

            return null;
        }

        foreach ($toWrite as list($row, $value)) {
            $row->set('is_fit', $value);
            // Without validation: is_fit is the only field being written, and
            // a legacy row with some other problem should not make the marking
            // fail silently.
            if (TableRegistry::getTableLocator()->get('MasterMedicalCheckUpResults')
                ->save($row, ['checkRules' => false, 'validate' => false])) {
                $this->out(sprintf('  <success>marked</success>  #%-3s %s -> %s', $row->id, $row->get('title'), $this->_label($value)));
            } else {
                $this->err(sprintf('  <error>failed</error>  #%-3s %s', $row->id, $row->get('title')));
            }
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->_recalculate();

        return null;
    }

    /**
     * Recalculate every candidate's and apprentice's medical standing.
     *
     * The standings were all worked out while no result was marked, so they
     * all say "nobody has said". Marking the results changes nothing for
     * anybody already on file until this runs.
     *
     * @return void
     */
    protected function _recalculate()
    {
        $locator = TableRegistry::getTableLocator();

        foreach ([
            'Candidates' => 'candidates',
            'Apprentices' => 'apprentices',
        ] as $alias => $label) {
            $this->out('');
            $this->out(sprintf('<info>Recalculating %s</info>', $label));

            try {
                $table = $locator->get($alias);
                if (!$table->getSchema()->hasColumn('mcu_result')) {
                    $this->out('  <warning>no mcu_result column - skipped</warning>');
                    continue;
                }
                $ids = $table->find()->select(['id'])->enableHydration(false)->extract('id')->toList();
            } catch (\Exception $e) {
                $this->err('  could not be read: ' . $e->getMessage());
                continue;
            }

            $counts = ['pass' => 0, 'fail' => 0, 'unknown' => 0];
            foreach ($ids as $id) {
                try {
                    $standing = $table->refreshMcuStanding($id);
                } catch (\Throwable $e) {
                    $standing = null;
                }
                $counts[$standing === null ? 'unknown' : $standing]++;
            }

            $this->out(sprintf('  %-28s %d', 'fit', $counts['pass']));
            $this->out(sprintf('  %-28s %d', 'not fit', $counts['fail']));
            $this->out(sprintf('  %-28s %d', 'no check-up on file yet', $counts['unknown']));
        }

        $this->out('');
        $this->out('A candidate marked not fit cannot be put forward for promotion.');
        $this->out('An apprentice marked not fit cannot be recorded as having departed.');
        $this->out('Both refusals name the reason on screen.');
    }

    /**
     * @param int|string|null $value The is_fit value.
     * @return string
     */
    protected function _label($value)
    {
        if ($value === null || $value === '') {
            return 'nobody said';
        }

        return (int)$value === 1 ? 'fit' : 'not fit';
    }
}
