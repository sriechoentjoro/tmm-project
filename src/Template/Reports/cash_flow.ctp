<?php
/**
 * Cash Flow - monthly journal debit/credit totals with trend bars
 *
 * @var \App\View\AppView $this
 * @var array $months
 */
$this->assign('title', 'Cash Flow');

$money = function ($amount) {
    $amount = (float)$amount;
    return ($amount < 0 ? '-' : '') . 'Rp ' . number_format(abs($amount), 0, ',', '.');
};

$totalDebit = 0;
$totalCredit = 0;
$totalEntries = 0;
$maxVolume = 1;
foreach ($months as $m) {
    $totalDebit += (float)$m['total_debit'];
    $totalCredit += (float)$m['total_credit'];
    $totalEntries += (int)$m['entries'];
    $maxVolume = max($maxVolume, (float)$m['total_debit'], (float)$m['total_credit']);
}

$monthLabel = function ($ym) {
    $timestamp = strtotime($ym . '-01');
    return $timestamp ? date('M Y', $timestamp) : $ym;
};
?>

<div class="cashflow-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-exchange"></i> <?= __('Cash Flow') ?></h2>
            <p class="text-muted"><?= __('Monthly journal debit / credit volume') ?></p>
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
        <div class="summary-card summary-purple">
            <span class="summary-number"><?= number_format($totalEntries) ?></span>
            <span class="summary-label"><i class="fa fa-book"></i> <?= __('Journal Entries') ?></span>
        </div>
        <div class="summary-card summary-blue">
            <span class="summary-number summary-money"><?= $money($totalDebit) ?></span>
            <span class="summary-label"><i class="fa fa-arrow-circle-down"></i> <?= __('Total Debit') ?></span>
        </div>
        <div class="summary-card summary-pink">
            <span class="summary-number summary-money"><?= $money($totalCredit) ?></span>
            <span class="summary-label"><i class="fa fa-arrow-circle-up"></i> <?= __('Total Credit') ?></span>
        </div>
        <div class="summary-card summary-green">
            <span class="summary-number summary-money"><?= $money($totalDebit - $totalCredit) ?></span>
            <span class="summary-label"><i class="fa fa-line-chart"></i> <?= __('Net') ?></span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($months)): ?>
                <?php foreach ($months as $m): ?>
                    <?php
                    $debit = (float)$m['total_debit'];
                    $credit = (float)$m['total_credit'];
                    $net = $debit - $credit;
                    ?>
                    <div class="month-row">
                        <div class="month-head">
                            <strong><?= h($monthLabel($m['month'])) ?></strong>
                            <span class="month-meta">
                                <?= number_format($m['entries']) ?> <?= __('entries') ?>
                                &middot; <?= __('net') ?>
                                <strong class="<?= $net >= 0 ? 'text-success' : 'text-danger' ?>"><?= $money($net) ?></strong>
                            </span>
                        </div>
                        <div class="bar-line">
                            <span class="bar-tag bar-tag-debit"><?= __('Debit') ?></span>
                            <div class="bar-track">
                                <div class="bar-fill bar-debit" style="width: <?= $maxVolume > 0 ? max(1, round($debit / $maxVolume * 100)) : 0 ?>%"></div>
                            </div>
                            <span class="bar-value"><?= $money($debit) ?></span>
                        </div>
                        <div class="bar-line">
                            <span class="bar-tag bar-tag-credit"><?= __('Credit') ?></span>
                            <div class="bar-track">
                                <div class="bar-fill bar-credit" style="width: <?= $maxVolume > 0 ? max(1, round($credit / $maxVolume * 100)) : 0 ?>%"></div>
                            </div>
                            <span class="bar-value"><?= $money($credit) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted py-4">
                    <i class="fa fa-exchange fa-3x"></i><br>
                    <?= __('No journal entries yet - the monthly cash flow will appear here.') ?><br>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Journal Entry'),
                        ['controller' => 'Journals', 'action' => 'add'],
                        ['escape' => false, 'class' => 'btn btn-success', 'style' => 'margin-top:10px;']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.cashflow-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.cashflow-page .page-header h2 { margin: 0 0 5px 0; }
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
.summary-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-blue   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-pink   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-green  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-number { font-size: 26px; font-weight: bold; line-height: 1.1; }
.summary-money { font-size: 19px; }
.summary-label { font-size: 13px; opacity: 0.95; }
.cashflow-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.cashflow-page .card-body { padding: 20px; }
.month-row {
    padding: 14px 0;
    border-bottom: 1px solid #f0f0f0;
}
.month-row:last-child { border-bottom: none; }
.month-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 4px;
}
.month-meta { color: #6c757d; font-size: 0.9em; }
.bar-line {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}
.bar-tag {
    flex: 0 0 52px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.bar-tag-debit { color: #2b7fd4; }
.bar-tag-credit { color: #c0392b; }
.bar-track {
    flex: 1;
    height: 12px;
    background: #f1f3f5;
    border-radius: 6px;
    overflow: hidden;
}
.bar-fill { height: 100%; border-radius: 6px; }
.bar-debit { background: linear-gradient(90deg, #4facfe, #00f2fe); }
.bar-credit { background: linear-gradient(90deg, #f093fb, #f5576c); }
.bar-value { flex: 0 0 140px; text-align: right; font-size: 0.9em; white-space: nowrap; }
.text-success { color: #28a745; }
.text-danger { color: #dc3545; }
</style>
