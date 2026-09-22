<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Repair installment chains that were left inconsistent.
 *
 * Every installment row carries the accumulated total and the outstanding
 * balance as they stood when it was written, each worked out from the row
 * before it. Deleting or editing a payment from the middle used to leave every
 * later row untouched, so the figures stopped adding up - and the tracking
 * page reads the LAST row, which means a trainee could be shown as owing
 * something none of their payments come to.
 *
 * Adding, editing and deleting now rebuild the chain as they go. This is for
 * the rows already on file from before that, and as a check afterwards: it
 * recomputes every trainee's chain and reports the ones whose stored figures
 * did not match.
 *
 * Report only unless --apply is given.
 *
 * Usage:
 *     bin/cake rebuild_installment_chains          report the mismatches
 *     bin/cake rebuild_installment_chains --apply  rewrite them
 */
class RebuildInstallmentChainsShell extends Shell
{
    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Recompute the running totals on every trainee installment chain.')
            ->addOption('apply', [
                'help' => 'Rewrite the rows that do not add up. Without it they are only reported.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');
        $installments = TableRegistry::getTableLocator()->get('TraineeInstallments');

        try {
            $traineeIds = $installments->find()
                ->select(['trainee_id'])
                ->distinct(['trainee_id'])
                ->enableHydration(false)
                ->extract('trainee_id')
                ->toList();
        } catch (\Exception $e) {
            $this->abort('Could not read the installments: ' . $e->getMessage());
        }

        $traineeIds = array_values(array_filter($traineeIds));
        $this->out('');
        $this->out(sprintf('<info>%d trainee(s)</info> with installments on file', count($traineeIds)));

        $names = [];
        try {
            foreach (TableRegistry::getTableLocator()->get('Trainees')->find()
                ->select(['id', 'name', 'tmm_code'])
                ->enableHydration(false) as $row) {
                $names[(int)$row['id']] = $row['name'] . ($row['tmm_code'] ? ' (' . $row['tmm_code'] . ')' : '');
            }
        } catch (\Exception $e) {
            // Names are a convenience; the ids still identify the chains.
        }

        $wrong = [];
        foreach ($traineeIds as $traineeId) {
            $traineeId = (int)$traineeId;
            $rows = $installments->find()
                ->where(['trainee_id' => $traineeId])
                ->order(['id' => 'ASC'])
                ->enableHydration(false)
                ->toArray();

            if (!$rows) {
                continue;
            }

            $full = 0;
            foreach ($rows as $row) {
                $full = max($full, (int)$row['full_payment_amount']);
            }

            $accumulated = 0;
            $mismatch = 0;
            foreach ($rows as $row) {
                $accumulated += max(0, (int)$row['payment_amount']);
                $unpaid = max(0, $full - $accumulated);
                $paidOff = $unpaid === 0 ? 1 : 0;

                if ((int)$row['payment_accummulated'] !== $accumulated
                    || (int)$row['unpaid_amount'] !== $unpaid
                    || (int)$row['is_paid_off'] !== $paidOff
                    || (int)$row['full_payment_amount'] !== $full
                ) {
                    $mismatch++;
                }
            }

            if ($mismatch) {
                $last = end($rows);
                $wrong[] = [
                    'trainee_id' => $traineeId,
                    'name' => $names[$traineeId] ?? ('#' . $traineeId),
                    'rows' => count($rows),
                    'mismatch' => $mismatch,
                    'stored_unpaid' => (int)$last['unpaid_amount'],
                    'real_unpaid' => max(0, $full - $accumulated),
                ];
            }
        }

        if (!$wrong) {
            $this->out('  <success>every chain adds up</success>');

            return null;
        }

        $this->out('');
        $this->out(sprintf('<warning>%d chain(s) do not add up</warning>', count($wrong)));
        $this->out(sprintf('  %-34s %-7s %-16s %s', 'trainee', 'rows', 'says outstanding', 'really outstanding'));
        foreach ($wrong as $row) {
            $this->out(sprintf('  %-34s %-7s %-16s %s',
                mb_substr($row['name'], 0, 33),
                $row['mismatch'] . '/' . $row['rows'],
                number_format($row['stored_unpaid'], 0, ',', '.'),
                number_format($row['real_unpaid'], 0, ',', '.')
            ));
        }

        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to rewrite them.');
            $this->out('The payments themselves are never touched - only the running');
            $this->out('totals that should have followed from them.');

            return null;
        }

        foreach ($wrong as $row) {
            $summary = $installments->rebuildChain($row['trainee_id']);
            $this->out(sprintf('  <success>rebuilt</success>  %-34s %d row(s), Rp %s outstanding',
                mb_substr($row['name'], 0, 33),
                $summary['rows'],
                number_format($summary['unpaid'], 0, ',', '.')
            ));
        }

        $this->out('');
        $this->out(sprintf('<success>%d chain(s) rebuilt.</success>', count($wrong)));

        return null;
    }
}
