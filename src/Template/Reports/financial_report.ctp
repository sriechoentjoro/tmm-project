<?php
/**
 * Financial report (shared by Income Statement and Balance Sheet)
 *
 * @var \App\View\AppView $this
 * @var array $rows        per-account debit/credit/balance
 * @var string $reportTitle
 */
$this->assign('title', $reportTitle);

$money = function ($amount) {
    $amount = (float)$amount;
    return ($amount < 0 ? '-' : '') . 'Rp ' . number_format(abs($amount), 0, ',', '.');
};

$isIncome = stripos($reportTitle, 'Income') !== false;

// Group figures per type. Natural balances: revenue/liability/equity are credit-normal,
// asset/expense are debit-normal.
$byType = [];
foreach ($rows as $r) {
    $type = ucfirst(strtolower($r['type']));
    if (!isset($byType[$type])) {
        $byType[$type] = ['debit' => 0, 'credit' => 0, 'accounts' => 0];
    }
    $byType[$type]['debit'] += (float)$r['total_debit'];
    $byType[$type]['credit'] += (float)$r['total_credit'];
    $byType[$type]['accounts']++;
}
$creditNormal = function ($type) {
    return in_array(strtolower($type), ['revenue', 'income', 'liability', 'equity']);
};
$naturalBalance = function ($type, $debit, $credit) use ($creditNormal) {
    return $creditNormal($type) ? $credit - $debit : $debit - $credit;
};

if ($isIncome) {
    $revenue = 0;
    $expense = 0;
    foreach ($byType as $type => $sums) {
        if ($creditNormal($type)) {
            $revenue += $sums['credit'] - $sums['debit'];
        } else {
            $expense += $sums['debit'] - $sums['credit'];
        }
    }
    $net = $revenue - $expense;
    $summaryCards = [
        ['label' => __('Revenue'), 'value' => $money($revenue), 'class' => 'summary-green', 'icon' => 'fa-arrow-circle-down'],
        ['label' => __('Expenses'), 'value' => $money($expense), 'class' => 'summary-pink', 'icon' => 'fa-arrow-circle-up'],
        ['label' => $net >= 0 ? __('Net Profit') : __('Net Loss'), 'value' => $money($net),
         'class' => $net >= 0 ? 'summary-blue' : 'summary-pink', 'icon' => $net >= 0 ? 'fa-smile-o' : 'fa-frown-o'],
    ];
} else {
    $assets = isset($byType['Asset']) ? $byType['Asset']['debit'] - $byType['Asset']['credit'] : 0;
    $liabilities = isset($byType['Liability']) ? $byType['Liability']['credit'] - $byType['Liability']['debit'] : 0;
    $equity = isset($byType['Equity']) ? $byType['Equity']['credit'] - $byType['Equity']['debit'] : 0;
    $difference = $assets - ($liabilities + $equity);
    $summaryCards = [
        ['label' => __('Assets'), 'value' => $money($assets), 'class' => 'summary-blue', 'icon' => 'fa-university'],
        ['label' => __('Liabilities'), 'value' => $money($liabilities), 'class' => 'summary-pink', 'icon' => 'fa-credit-card'],
        ['label' => __('Equity'), 'value' => $money($equity), 'class' => 'summary-purple', 'icon' => 'fa-balance-scale'],
        ['label' => abs($difference) < 0.01 ? __('Balanced') : __('Out of Balance'),
         'value' => abs($difference) < 0.01 ? '✓' : $money($difference),
         'class' => abs($difference) < 0.01 ? 'summary-green' : 'summary-pink',
         'icon' => abs($difference) < 0.01 ? 'fa-check-circle' : 'fa-exclamation-triangle'],
    ];
}

$typeBadge = [
    'asset' => 'type-asset', 'liability' => 'type-liability', 'equity' => 'type-equity',
    'revenue' => 'type-revenue', 'income' => 'type-revenue', 'expense' => 'type-expense',
];
?>

<div class="finreport-page">
    <div class="page-header">
        <div>
            <h2><i class="fa <?= $isIncome ? 'fa-line-chart' : 'fa-balance-scale' ?>"></i> <?= h($reportTitle) ?></h2>
            <p class="text-muted"><?= __('Aggregated from journal details per account - post journals to update the figures') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-bar-chart"></i> ' . __('All Reports'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link('<i class="fa fa-book"></i> ' . __('Journals'),
                ['controller' => 'Journals', 'action' => 'index'],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <?php foreach ($summaryCards as $card): ?>
            <div class="summary-card <?= $card['class'] ?>">
                <span class="summary-number summary-money"><?= h($card['value']) ?></span>
                <span class="summary-label"><i class="fa <?= $card['icon'] ?>"></i> <?= h($card['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="table-scroll-wrapper" style="overflow-x: auto;">
            <table class="table rich-table">
                <thead>
                    <tr>
                        <th><?= __('Code') ?></th>
                        <th><?= __('Account') ?></th>
                        <th><?= __('Type') ?></th>
                        <th class="text-right"><?= __('Debit') ?></th>
                        <th class="text-right"><?= __('Credit') ?></th>
                        <th class="text-right"><?= __('Balance') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <?php $balance = $naturalBalance($r['type'], (float)$r['total_debit'], (float)$r['total_credit']); ?>
                    <tr>
                        <td><code class="account-code"><?= h($r['code']) ?></code></td>
                        <td><strong><?= h($r['name']) ?></strong></td>
                        <td>
                            <span class="type-badge <?= isset($typeBadge[strtolower($r['type'])]) ? $typeBadge[strtolower($r['type'])] : '' ?>">
                                <?= h(ucfirst($r['type'])) ?>
                            </span>
                        </td>
                        <td class="text-right"><?= $money($r['total_debit']) ?></td>
                        <td class="text-right"><?= $money($r['total_credit']) ?></td>
                        <td class="text-right <?= $balance < 0 ? 'text-danger' : '' ?>"><strong><?= $money($balance) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="empty-cell">
                            <?= __('No accounts of this type yet.') ?>
                            <?= $this->Html->link(__('Define them under Chart of Accounts'),
                                ['controller' => 'ChartOfAccounts', 'action' => 'index']) ?>.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted footnote">
        <i class="fa fa-info-circle"></i>
        <?= __('Balance uses each account\'s natural side: debit-normal for assets and expenses, credit-normal for revenue, liabilities and equity.') ?>
    </p>
</div>

<style>
.finreport-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.finreport-page .page-header h2 { margin: 0 0 5px 0; }
.summary-strip { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.summary-card {
    flex: 1;
    min-width: 170px;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.summary-green  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-pink   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-blue   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-number { font-size: 24px; font-weight: bold; line-height: 1.1; }
.summary-money { font-size: 20px; }
.summary-label { font-size: 13px; opacity: 0.95; }
.finreport-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 12px;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 750px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th { padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap; text-align: left; }
.rich-table td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .text-right { text-align: right; white-space: nowrap; }
.rich-table .empty-cell { padding: 25px; color: #6c757d; text-align: center; }
.account-code {
    background: #f1f3f9;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: bold;
    color: #3b4890;
}
.type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: bold;
    white-space: nowrap;
}
.type-asset     { background: #e8f4fd; color: #2b7fd4; }
.type-liability { background: #fdeaea; color: #c0392b; }
.type-equity    { background: #f7f3fb; color: #764ba2; }
.type-revenue   { background: #e6f9ee; color: #1e7e42; }
.type-expense   { background: #fdf1e3; color: #e67e22; }
.text-danger { color: #dc3545; }
.footnote { font-size: 0.85em; }
</style>
