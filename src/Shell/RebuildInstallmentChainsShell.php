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
 *     bin/cake rebuild_installment_chains --show=7 read one trainee's rows
 *     bin/cake rebuild_installment_chains --apply  rewrite them
 *
 * --show first. A mismatch says the stored figures and the payments disagree;
 * it cannot say which of the two is wrong, and only the payment column can.
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
            ])
            ->addOption('show', [
                'help' => 'Print one trainee\'s rows, stored figures beside recomputed ones, and change nothing.',
            ]);
    }

    /**
     * Print one trainee's chain, stored figures beside recomputed ones.
     *
     * A summary can only say the two disagree. It cannot say which of them is
     * right, and the difference between the two readings is the difference
     * between correcting a stale total and telling somebody who has paid that
     * they still owe it. So before anything is rewritten, this puts the rows
     * on the screen and changes nothing.
     *
     * @param int $traineeId Trainee id.
     * @param \Cake\ORM\Table $installments The installments table.
     * @return int|null
     */
    protected function show($traineeId, $installments)
    {
        $rows = $installments->find()
            ->where(['trainee_id' => $traineeId])
            ->order(['id' => 'ASC'])
            ->enableHydration(false)
            ->toArray();

        if (!$rows) {
            $this->out(sprintf('<warning>No installments for trainee %d.</warning>', $traineeId));

            return null;
        }

        $name = '#' . $traineeId;
        try {
            $trainee = TableRegistry::getTableLocator()->get('Trainees')->find()
                ->where(['id' => $traineeId])->enableHydration(false)->first();
            if ($trainee) {
                $name = $trainee['name'] . ($trainee['tmm_code'] ? ' (' . $trainee['tmm_code'] . ')' : '');
            }
        } catch (\Exception $e) {
        }

        $full = 0;
        foreach ($rows as $row) {
            $full = max($full, (int)$row['full_payment_amount']);
        }

        $this->out('');
        $this->out(sprintf('<info>%s</info> - %d row(s), owing cost %s',
            $name, count($rows), number_format($full, 0, ',', '.')));
        $this->out('');
        $this->out('  ' . sprintf('%-5s %-12s %14s %6s | %14s %14s %4s | %14s %14s %4s',
            'id', 'date', 'payment', 'cat', 'accum stored', 'unpaid stored', 'off',
            'accum should', 'unpaid should', 'off'));
        $this->out('  ' . str_repeat('-', 125));

        $accumulated = 0;
        foreach ($rows as $row) {
            $accumulated += max(0, (int)$row['payment_amount']);
            $unpaid = max(0, $full - $accumulated);
            $shouldOff = $unpaid === 0 ? 1 : 0;
            $differs = (int)$row['payment_accummulated'] !== $accumulated
                || (int)$row['unpaid_amount'] !== $unpaid
                || (int)$row['is_paid_off'] !== $shouldOff;

            $this->out(sprintf('  %s%-5s %-12s %14s %6s | %14s %14s %4s | %14s %14s %4s%s',
                $differs ? '<warning>' : '',
                $row['id'],
                substr((string)$row['payment_date'], 0, 10),
                number_format((int)$row['payment_amount'], 0, ',', '.'),
                $row['master_transaction_category_id'],
                number_format((int)$row['payment_accummulated'], 0, ',', '.'),
                number_format((int)$row['unpaid_amount'], 0, ',', '.'),
                (int)$row['is_paid_off'] ? 'yes' : 'no',
                number_format($accumulated, 0, ',', '.'),
                number_format($unpaid, 0, ',', '.'),
                $shouldOff ? 'yes' : 'no',
                $differs ? '</warning>' : ''
            ));
        }

        $this->out('');
        $this->out(sprintf('  payments on file add up to <info>%s</info>, leaving <info>%s</info> of the %s owing cost',
            number_format($accumulated, 0, ',', '.'),
            number_format(max(0, $full - $accumulated), 0, ',', '.'),
            number_format($full, 0, ',', '.')));
        $this->out('');
        $this->out('  Read the payment column first. If those amounts are what the trainee');
        $this->out('  really handed over, the right-hand figures are correct and --apply is');
        $this->out('  safe. If an accumulated total was ever typed into a payment field, or');
        $this->out('  a payment is missing from this list, fix the payment itself first -');
        $this->out('  --apply would build on the wrong numbers.');
        $this->out('');
        $this->out('<info>Nothing changed.</info>');

        return null;
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');
        $installments = TableRegistry::getTableLocator()->get('TraineeInstallments');

        if ($this->param('show') !== null && $this->param('show') !== false) {
            return $this->show((int)$this->param('show'), $installments);
        }

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
        $this->out(sprintf('  %-6s %-30s %-7s %18s %18s',
            'id', 'trainee', 'rows', 'says outstanding', 'really outstanding'));
        foreach ($wrong as $row) {
            $this->out(sprintf('  %-6s %-30s %-7s %18s %18s',
                $row['trainee_id'],
                mb_substr($row['name'], 0, 29),
                $row['mismatch'] . '/' . $row['rows'],
                number_format($row['stored_unpaid'], 0, ',', '.'),
                number_format($row['real_unpaid'], 0, ',', '.')
            ));
        }

        $this->out('');

        if (!$apply) {
            $this->out('<info>Nothing changed.</info>');
            $this->out('');
            $this->out('Read a chain before rewriting it:');
            foreach ($wrong as $row) {
                $this->out(sprintf('  bin/cake rebuild_installment_chains --show=%d   %s',
                    $row['trainee_id'], mb_substr($row['name'], 0, 40)));
            }
            $this->out('');
            $this->out('A mismatch means the stored totals and the payments disagree. It does');
            $this->out('not say which of them is right. Recomputing takes the payment column');
            $this->out('as the truth, so if a payment is missing or an accumulated total was');
            $this->out('once typed into a payment field, --apply would spread that mistake');
            $this->out('across the chain instead of correcting it.');
            $this->out('');
            $this->out('Once the payments read correctly, --apply rewrites only the running');
            $this->out('totals. The payments themselves are never touched.');

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
