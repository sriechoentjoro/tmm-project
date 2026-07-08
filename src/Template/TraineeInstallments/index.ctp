<?php
/**
 * Trainee Installments - payment ledger
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment[]|\Cake\Collection\CollectionInterface $traineeInstallments
 * @var array $trainees                    [id => "Name (TMM-code)"]
 * @var array $mastertransactioncategories [id => title]
 * @var array $summary                     ['payments','received','settled','open']
 * @var int $filterTrainee
 * @var int $filterCategory
 * @var string $filterPaid
 */
$this->assign('title', 'Trainee Installments');

$money = function ($amount, $currency = null) {
    $prefix = ($currency && $currency !== 'IDR') ? $currency . ' ' : 'Rp ';
    return $prefix . number_format((int)$amount, 0, ',', '.');
};
$formatDate = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('d M Y');
    }
    $timestamp = $value ? strtotime((string)$value) : false;
    return $timestamp ? date('d M Y', $timestamp) : '-';
};

$hasFilter = $filterTrainee || $filterCategory || $filterPaid !== '';
?>

<div class="installments-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-money"></i> <?= __('Trainee Installments') ?></h2>
            <p class="text-muted"><?= __('Every payment recorded - see Payment Tracking for per-trainee balances') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Payment'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-line-chart"></i> ' . __('Tracking'),
                ['action' => 'tracking'], ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-file-text-o"></i> ' . __('Receipts'),
                ['action' => 'receipts'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link('<i class="fa fa-download"></i> CSV',
                ['action' => 'exportCsv', '?' => $this->request->getQueryParams()],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Excel',
                ['action' => 'exportExcel', '?' => $this->request->getQueryParams()],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link('<i class="fa fa-print"></i> ' . __('Print'),
                ['action' => 'printReport', '?' => $this->request->getQueryParams()],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['payments']) ?></span>
            <span class="summary-label"><i class="fa fa-list"></i> <?= __('Payments Recorded') ?></span>
        </div>
        <div class="summary-card summary-received">
            <span class="summary-number summary-money"><?= $money($summary['received']) ?></span>
            <span class="summary-label"><i class="fa fa-arrow-circle-down"></i> <?= __('Total Received') ?></span>
        </div>
        <div class="summary-card summary-settled">
            <span class="summary-number"><?= number_format($summary['settled']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Trainees Settled') ?></span>
        </div>
        <div class="summary-card summary-open">
            <span class="summary-number"><?= number_format($summary['open']) ?></span>
            <span class="summary-label"><i class="fa fa-hourglass-half"></i> <?= __('Still Paying') ?></span>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="get" class="filter-form">
            <div class="filter-field">
                <label><?= __('Trainee') ?></label>
                <select name="trainee_id" onchange="this.form.submit()">
                    <option value=""><?= __('All trainees') ?></option>
                    <?php foreach ($trainees as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterTrainee == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Transaction Type') ?></label>
                <select name="category_id" onchange="this.form.submit()">
                    <option value=""><?= __('All types') ?></option>
                    <?php foreach ($mastertransactioncategories as $id => $title): ?>
                        <option value="<?= $id ?>" <?= $filterCategory == $id ? 'selected' : '' ?>><?= h($title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Status') ?></label>
                <select name="paid" onchange="this.form.submit()">
                    <option value=""><?= __('All payments') ?></option>
                    <option value="1" <?= $filterPaid === '1' ? 'selected' : '' ?>><?= __('Settling payment (receipt)') ?></option>
                    <option value="0" <?= $filterPaid === '0' ? 'selected' : '' ?>><?= __('Installment') ?></option>
                </select>
            </div>
            <?php if ($hasFilter): ?>
                <div class="filter-field">
                    <label>&nbsp;</label>
                    <?= $this->Html->link('<i class="fa fa-times"></i> ' . __('Reset'),
                        ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php $rows = iterator_to_array($traineeInstallments); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                            <th><?= $this->Paginator->sort('trainee_id', __('Trainee')) ?></th>
                            <th><?= $this->Paginator->sort('master_transaction_category_id', __('Type')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('payment_amount', __('Payment')) ?></th>
                            <th><?= $this->Paginator->sort('payment_date', __('Date')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('payment_accummulated', __('Accumulated')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('full_payment_amount', __('Owing Cost')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('unpaid_amount', __('Remaining')) ?></th>
                            <th><?= $this->Paginator->sort('is_paid_off', __('Status')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <?php
                        $currency = $row->has('master_currency') ? $row->master_currency->title : 'IDR';
                        $percent = $row['full_payment_amount'] > 0
                            ? min(100, (int)round($row['payment_accummulated'] / $row['full_payment_amount'] * 100))
                            : 0;
                        ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td class="text-muted">#<?= h($row['id']) ?></td>
                            <td>
                                <strong><?= $row->has('trainee') ? h($row->trainee->name) : ('#' . h($row['trainee_id'])) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-outline">
                                    <?= $row->has('master_transaction_category') ? h($row->master_transaction_category->title) : '-' ?>
                                </span>
                            </td>
                            <td class="text-right"><strong><?= $money($row['payment_amount'], $currency) ?></strong></td>
                            <td><?= h($formatDate($row['payment_date'])) ?></td>
                            <td class="text-right">
                                <?= $money($row['payment_accummulated'], $currency) ?>
                                <div class="mini-track">
                                    <div class="mini-fill" style="width: <?= $percent ?>%;
                                        background: <?= $percent >= 100 ? '#43e97b' : ($percent >= 50 ? '#4facfe' : '#f5a623') ?>;"></div>
                                </div>
                            </td>
                            <td class="text-right"><?= $money($row['full_payment_amount'], $currency) ?></td>
                            <td class="text-right <?= $row['unpaid_amount'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                <?= $money($row['unpaid_amount'], $currency) ?>
                            </td>
                            <td>
                                <?php if ($row['is_paid_off']): ?>
                                    <span class="badge badge-success"><i class="fa fa-check"></i> <?= __('Receipt') ?></span>
                                <?php elseif ((int)$row['payment_amount'] === 0 && (int)$row['payment_accummulated'] === 0): ?>
                                    <span class="badge badge-info"><i class="fa fa-flag"></i> <?= __('Owing Cost Set') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= __('Installment') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="paginator" style="margin-top: 15px;">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-money fa-4x"></i>
                <h4><?= $hasFilter ? __('No payments match the current filter.') : __('No installment payments yet.') ?></h4>
                <p class="text-muted"><?= __('Record a trainee\'s owing cost with their first payment - the system tracks the balance until the receipt is issued.') ?></p>
                <div>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Payment'),
                        ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
                    <?php if ($hasFilter): ?>
                        <?= $this->Html->link(__('Reset Filter'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.installments-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.installments-page .page-header h2 { margin: 0 0 5px 0; }
.page-header-actions { display: flex; gap: 6px; flex-wrap: wrap; }
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
.summary-total    { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-received { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-settled  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-open     { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-money { font-size: 20px; }
.summary-label { font-size: 13px; opacity: 0.95; }
.filter-bar {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px 20px;
    margin-bottom: 20px;
}
.filter-form { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
.filter-field { display: flex; flex-direction: column; min-width: 180px; }
.filter-field label { font-size: 12px; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
.filter-field select {
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
}
.installments-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 1050px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th { padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap; text-align: left; }
.rich-table td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; vertical-align: middle; white-space: nowrap; }
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .text-right { text-align: right; }
.rich-table .actions { white-space: nowrap; }
.rich-table th.actions,
.rich-table td.actions {
    position: sticky;
    left: 0;
    z-index: 5;
    width: 1%;
    padding-left: 10px;
    padding-right: 10px;
}
.rich-table th.actions { background: #e9ecfa; z-index: 6; }
.rich-table td.actions { background: #fff; }
.rich-table tbody tr:hover td.actions { background: #f8f9ff; }
.rich-table td.actions .btn { padding: 4px 8px; }
.table-scroll-wrapper.is-scrolled .rich-table th.actions,
.table-scroll-wrapper.is-scrolled .rich-table td.actions {
    border-right: 2px solid #d3d9f5;
    box-shadow: 4px 0 8px -2px rgba(60, 70, 130, 0.18);
}
.mini-track {
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 4px;
}
.mini-fill { height: 100%; border-radius: 3px; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-info { background: #17a2b8; color: white; }
.badge-outline { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.text-danger { color: #dc3545; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
.empty-state .btn { margin: 5px; }
</style>

<script>
(function () {
    document.querySelectorAll('.installments-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>
