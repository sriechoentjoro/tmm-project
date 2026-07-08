<?php
/**
 * Journal view - header + double-entry lines
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Journal $journal
 * @var array $lines   double-entry lines with account name/type
 */
$this->assign('title', 'Journal ' . ($journal->reference_no ?: ('#' . $journal->id)));

$money = function ($amount) {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
};
$formatDate = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('d M Y');
    }
    return $value ? (string)$value : '-';
};
$badge = $journal->status === 'Posted' ? 'success' : ($journal->status === 'Draft' ? 'warning' : 'danger');
$totalDebit = 0;
$totalCredit = 0;
foreach ($lines as $l) {
    $totalDebit += (float)$l['debit'];
    $totalCredit += (float)$l['credit'];
}
$balanced = round($totalDebit, 2) === round($totalCredit, 2);
$typeClass = [
    'Asset' => 'type-asset', 'Liability' => 'type-liability', 'Equity' => 'type-equity',
    'Revenue' => 'type-revenue', 'Expense' => 'type-expense',
];
?>

<div class="journal-view-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-book"></i> <?= h($journal->reference_no ?: ('Journal #' . $journal->id)) ?></h2>
            <p class="text-muted"><?= h($journal->description ?: __('No description')) ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-edit"></i> ' . __('Edit'),
                ['action' => 'edit', $journal->id], ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-book"></i> ' . __('All Journals'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body meta-grid">
            <div><span class="meta-label"><?= __('Date') ?></span><strong><?= h($formatDate($journal->transaction_date)) ?></strong></div>
            <div><span class="meta-label"><?= __('Reference') ?></span><strong><?= h($journal->reference_no ?: '-') ?></strong></div>
            <div><span class="meta-label"><?= __('Status') ?></span><span class="badge badge-<?= $badge ?>"><?= h($journal->status) ?></span></div>
            <div><span class="meta-label"><?= __('Balanced') ?></span>
                <?php if ($balanced && $totalDebit > 0): ?>
                    <span class="badge badge-success"><i class="fa fa-check"></i> <?= __('Yes') ?></span>
                <?php else: ?>
                    <span class="badge badge-warning"><i class="fa fa-exclamation-triangle"></i> <?= __('Check') ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4><i class="fa fa-list"></i> <?= __('Double-Entry Lines') ?></h4></div>
        <div class="card-body">
            <?php if (!empty($lines)): ?>
                <table class="lines-table">
                    <thead>
                        <tr>
                            <th><?= __('Account') ?></th>
                            <th><?= __('Type') ?></th>
                            <th><?= __('Line Note') ?></th>
                            <th class="text-right"><?= __('Debit') ?></th>
                            <th class="text-right"><?= __('Credit') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lines as $l): ?>
                        <tr>
                            <td><code class="acc-code"><?= h($l['code']) ?></code> <?= h($l['name']) ?></td>
                            <td><span class="type-badge <?= isset($typeClass[$l['type']]) ? $typeClass[$l['type']] : '' ?>"><?= h($l['type']) ?></span></td>
                            <td class="text-muted"><?= h($l['description'] ?: '-') ?></td>
                            <td class="text-right"><?= (float)$l['debit'] > 0 ? '<strong>' . $money($l['debit']) . '</strong>' : '-' ?></td>
                            <td class="text-right"><?= (float)$l['credit'] > 0 ? '<strong>' . $money($l['credit']) . '</strong>' : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="totals-row">
                            <td colspan="3" class="text-right"><strong><?= __('Totals') ?></strong></td>
                            <td class="text-right"><strong><?= $money($totalDebit) ?></strong></td>
                            <td class="text-right"><strong><?= $money($totalCredit) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <p class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-2x"></i><br>
                    <?= __('This journal has no double-entry lines.') ?>
                    <?= __('It only carries header totals (Debit {0} / Credit {1}).',
                        $money($journal->total_debit), $money($journal->total_credit)) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.journal-view-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.journal-view-page .page-header h2 { margin: 0 0 5px 0; }
.journal-view-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.journal-view-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.journal-view-page .card-header h4 { margin: 0; }
.journal-view-page .card-body { padding: 20px; }
.meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
}
.meta-grid > div { display: flex; flex-direction: column; gap: 4px; }
.meta-label { font-size: 0.8em; color: #6c757d; text-transform: uppercase; }
.lines-table { width: 100%; border-collapse: collapse; }
.lines-table th { text-align: left; padding: 10px; border-bottom: 2px solid #667eea; white-space: nowrap; }
.lines-table td { padding: 10px; border-bottom: 1px solid #e9ecef; }
.lines-table .text-right { text-align: right; white-space: nowrap; }
.totals-row td { border-top: 2px solid #667eea; }
.acc-code { background: #f1f3f9; padding: 2px 8px; border-radius: 5px; font-weight: bold; color: #3b4890; }
.type-badge {
    padding: 3px 9px;
    border-radius: 10px;
    font-size: 0.8em;
    font-weight: bold;
}
.type-asset { background: #e8f4fd; color: #2b7fd4; }
.type-liability { background: #fdeaea; color: #c0392b; }
.type-equity { background: #f7f3fb; color: #764ba2; }
.type-revenue { background: #e6f9ee; color: #1e7e42; }
.type-expense { background: #fdf1e3; color: #e67e22; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
}
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-danger { background: #dc3545; color: white; }
</style>
