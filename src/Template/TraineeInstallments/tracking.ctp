<?php
/**
 * Payment Tracking - per-trainee payment progress + installment history
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment[]|\Cake\Collection\CollectionInterface $installments
 * @var array $traineeNames    [id => "Name (TMM-code)"]
 * @var array $categoryNames   [id => title]
 * @var array $currencyNames   [id => code]
 * @var array $paymentProgress per-trainee latest running figures
 * @var array $summary         ['payingTrainees','paidOff','collected','outstanding']
 * @var int $filterTrainee
 * @var string $filterPaid
 */
$this->assign('title', 'Payment Tracking');

$money = function ($amount, $currency = 'IDR') {
    return ($currency === 'IDR' ? 'Rp ' : $currency . ' ') . number_format((int)$amount, 0, ',', '.');
};

$formatDate = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('d M Y');
    }
    $timestamp = $value ? strtotime((string)$value) : false;
    return $timestamp ? date('d M Y', $timestamp) : '-';
};

$hasFilter = $filterTrainee || $filterPaid === '1' || $filterPaid === '0';
?>

<div class="tracking-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-money"></i> <?= __('Payment Tracking') ?></h2>
            <p class="text-muted"><?= __('Trainee installment payments - who has paid, who still owes') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Installment'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-file-text-o"></i> ' . __('Receipts'),
                ['action' => 'receipts'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['payingTrainees']) ?></span>
            <span class="summary-label"><i class="fa fa-users"></i> <?= __('Paying Trainees') ?></span>
        </div>
        <div class="summary-card summary-paid">
            <span class="summary-number"><?= number_format($summary['paidOff']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Fully Paid Off') ?></span>
        </div>
        <div class="summary-card summary-collected">
            <span class="summary-number summary-money"><?= $money($summary['collected']) ?></span>
            <span class="summary-label"><i class="fa fa-arrow-circle-down"></i> <?= __('Collected') ?></span>
        </div>
        <div class="summary-card summary-outstanding">
            <span class="summary-number summary-money"><?= $money($summary['outstanding']) ?></span>
            <span class="summary-label"><i class="fa fa-exclamation-circle"></i> <?= __('Outstanding') ?></span>
        </div>
    </div>

    <?php if (!empty($traineesWithoutCost)): ?>
        <!-- Step 1: set the owing cost -->
        <div class="card owing-card">
            <div class="card-header">
                <h4><i class="fa fa-flag"></i> <?= __('Set Owing Cost') ?></h4>
                <small class="text-muted"><?= __('Step 1 - set what each trainee must pay before recording any payment') ?></small>
            </div>
            <div class="card-body">
                <div class="owing-grid">
                    <?php foreach ($traineesWithoutCost as $traineeId => $name): ?>
                        <?= $this->Form->create(null, ['url' => ['action' => 'setOwingCost'], 'class' => 'owing-item']) ?>
                            <?= $this->Form->hidden('trainee_id', ['value' => $traineeId, 'id' => false]) ?>
                            <span class="owing-name"><i class="fa fa-user-o"></i> <?= h($name) ?></span>
                            <input type="number" name="full_payment_amount" min="1" step="1" required
                                   placeholder="<?= __('Owing cost (Rp)') ?>" class="owing-input">
                            <?= $this->Form->button('<i class="fa fa-flag"></i> ' . __('Set'),
                                ['escapeTitle' => false, 'class' => 'btn btn-sm btn-success']) ?>
                        <?= $this->Form->end() ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Per-trainee payment progress -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fa fa-tasks"></i> <?= __('Payment Progress per Trainee') ?></h4>
            <small class="text-muted"><?= __('Largest outstanding balance first') ?></small>
        </div>
        <div class="card-body">
            <?php if (!empty($paymentProgress)): ?>
                <?php foreach ($paymentProgress as $row): ?>
                    <div class="progress-row">
                        <div class="progress-row-header">
                            <span class="progress-name">
                                <?php if ($row['is_paid_off']): ?>
                                    <i class="fa fa-check-circle text-success"></i>
                                <?php else: ?>
                                    <i class="fa fa-clock-o text-warning"></i>
                                <?php endif; ?>
                                <?= h($row['name']) ?>
                            </span>
                            <span class="progress-figures">
                                <?= $money($row['accumulated'], $row['currency']) ?> / <?= $money($row['full'], $row['currency']) ?>
                                <?php if ($row['is_paid_off']): ?>
                                    <span class="badge badge-success"><?= __('Paid Off') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= __('Owes {0}', $money($row['unpaid'], $row['currency'])) ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: <?= (int)$row['percent'] ?>%;
                                background: <?= $row['percent'] >= 100 ? '#43e97b' : ($row['percent'] >= 50 ? '#4facfe' : '#f5a623') ?>;"></div>
                        </div>
                        <small class="text-muted"><?= __('Last payment:') ?> <?= h($formatDate($row['last_payment'])) ?>
                            &middot; <a href="<?= $this->Url->build(['action' => 'tracking', '?' => ['trainee_id' => $row['trainee_id']]]) ?>"><?= __('view payments') ?></a>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-3x"></i><br>
                    <?= __('No installment payments recorded yet.') ?><br>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Record the first payment'),
                        ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success', 'style' => 'margin-top:10px;']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="get" class="filter-form">
            <div class="filter-field">
                <label><?= __('Trainee') ?></label>
                <select name="trainee_id" onchange="this.form.submit()">
                    <option value=""><?= __('All trainees') ?></option>
                    <?php foreach ($traineeNames as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $filterTrainee == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label><?= __('Status') ?></label>
                <select name="paid" onchange="this.form.submit()">
                    <option value=""><?= __('All payments') ?></option>
                    <option value="1" <?= $filterPaid === '1' ? 'selected' : '' ?>><?= __('Paid off') ?></option>
                    <option value="0" <?= $filterPaid === '0' ? 'selected' : '' ?>><?= __('Still owing') ?></option>
                </select>
            </div>
            <?php if ($hasFilter): ?>
                <div class="filter-field">
                    <label>&nbsp;</label>
                    <?= $this->Html->link('<i class="fa fa-times"></i> ' . __('Reset'),
                        ['action' => 'tracking'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php $rows = iterator_to_array($installments); ?>
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
                            <th class="text-right"><?= $this->Paginator->sort('payment_amount', __('Amount')) ?></th>
                            <th><?= $this->Paginator->sort('payment_date', __('Date')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('payment_accummulated', __('Accumulated')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('unpaid_amount', __('Remaining')) ?></th>
                            <th><?= $this->Paginator->sort('is_paid_off', __('Status')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <?php $currency = isset($currencyNames[$row['master_currency_id']]) ? $currencyNames[$row['master_currency_id']] : 'IDR'; ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td class="text-muted">#<?= h($row['id']) ?></td>
                            <td>
                                <strong><?= h(isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id'])) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-outline"><?= h(isset($categoryNames[$row['master_transaction_category_id']]) ? $categoryNames[$row['master_transaction_category_id']] : '-') ?></span>
                            </td>
                            <td class="text-right"><strong><?= $money($row['payment_amount'], $currency) ?></strong></td>
                            <td><?= h($formatDate($row['payment_date'])) ?></td>
                            <td class="text-right"><?= $money($row['payment_accummulated'], $currency) ?></td>
                            <td class="text-right <?= $row['unpaid_amount'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                <?= $money($row['unpaid_amount'], $currency) ?>
                            </td>
                            <td>
                                <?php if ($row['is_paid_off']): ?>
                                    <span class="badge badge-success"><?= __('Paid Off') ?></span>
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
    <?php elseif ($hasFilter): ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-search fa-3x"></i>
                <h4><?= __('No payments match the current filter.') ?></h4>
                <?= $this->Html->link(__('Reset Filter'), ['action' => 'tracking'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.tracking-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.tracking-page .page-header h2 { margin: 0 0 5px 0; }
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
.summary-total       { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-paid        { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-collected   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-outstanding { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-money { font-size: 20px; }
.summary-label { font-size: 13px; opacity: 0.95; }
.tracking-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.tracking-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.tracking-page .card-header h4 { margin: 0 0 3px 0; }
.tracking-page .card-body { padding: 20px; }
.owing-card { border-left: 5px solid #667eea; }
.owing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 10px;
}
.owing-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: #fafbff;
    border: 1px solid #e4e8fb;
    border-radius: 8px;
}
.owing-name { flex: 1; font-weight: bold; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.owing-input {
    width: 150px;
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
}
.progress-row { margin-bottom: 18px; }
.progress-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    flex-wrap: wrap;
    gap: 4px;
}
.progress-name { font-weight: bold; }
.progress-figures { font-size: 0.9em; color: #495057; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.progress-track { height: 10px; background: #e9ecef; border-radius: 5px; overflow: hidden; margin-bottom: 4px; }
.progress-fill { height: 100%; border-radius: 5px; transition: width 0.5s ease; }
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
.rich-table { border-collapse: collapse; width: 100%; min-width: 950px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th {
    padding: 12px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
    text-align: left;
}
.rich-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
    white-space: nowrap;
}
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .text-right { text-align: right; }
.rich-table .actions { white-space: nowrap; }
/* Floating (sticky) actions column - divider only while scrolled */
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
.badge-outline { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.text-danger { color: #dc3545; }
.text-success { color: #28a745; }
.text-warning { color: #f5a623; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
</style>

<script>
(function () {
    document.querySelectorAll('.tracking-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>
