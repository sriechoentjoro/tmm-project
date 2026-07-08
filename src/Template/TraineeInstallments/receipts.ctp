<?php
/**
 * Receipt Management - fully settled installment payments
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment[]|\Cake\Collection\CollectionInterface $installments
 * @var array $traineeNames  [id => "Name (TMM-code)"]
 * @var array $categoryNames [id => title]
 * @var array $currencyNames [id => code]
 * @var array $summary       ['receipts','traineesSettled','totalSettled']
 */
$this->assign('title', 'Receipts');

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
?>

<div class="receipts-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-file-text-o"></i> <?= __('Receipt Management') ?></h2>
            <p class="text-muted"><?= __('Payments that fully settled a trainee\'s balance') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-line-chart"></i> ' . __('Payment Tracking'),
                ['action' => 'tracking'], ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('All Installments'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['receipts']) ?></span>
            <span class="summary-label"><i class="fa fa-file-text-o"></i> <?= __('Receipts') ?></span>
        </div>
        <div class="summary-card summary-settled">
            <span class="summary-number"><?= number_format($summary['traineesSettled']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Trainees Settled') ?></span>
        </div>
        <div class="summary-card summary-amount">
            <span class="summary-number summary-money"><?= $money($summary['totalSettled']) ?></span>
            <span class="summary-label"><i class="fa fa-money"></i> <?= __('Total Settled Value') ?></span>
        </div>
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
                            <th class="text-right"><?= $this->Paginator->sort('payment_amount', __('Final Payment')) ?></th>
                            <th class="text-right"><?= $this->Paginator->sort('full_payment_amount', __('Total Settled')) ?></th>
                            <th><?= $this->Paginator->sort('payment_date', __('Settled On')) ?></th>
                            <th><?= __('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <?php $currency = isset($currencyNames[$row['master_currency_id']]) ? $currencyNames[$row['master_currency_id']] : 'IDR'; ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-print"></i>', ['action' => 'printReport', '?' => ['id' => $row['id']]],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary', 'title' => __('Print'), 'target' => '_blank']) ?>
                            </td>
                            <td class="text-muted">#<?= h($row['id']) ?></td>
                            <td>
                                <strong><?= h(isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id'])) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-outline"><?= h(isset($categoryNames[$row['master_transaction_category_id']]) ? $categoryNames[$row['master_transaction_category_id']] : '-') ?></span>
                            </td>
                            <td class="text-right"><?= $money($row['payment_amount'], $currency) ?></td>
                            <td class="text-right"><strong><?= $money($row['full_payment_amount'], $currency) ?></strong></td>
                            <td><?= h($formatDate($row['payment_date'])) ?></td>
                            <td><span class="badge badge-success"><i class="fa fa-check"></i> <?= __('Paid Off') ?></span></td>
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
                <i class="fa fa-file-text-o fa-4x"></i>
                <h4><?= __('No settled payments yet.') ?></h4>
                <p class="text-muted"><?= __('When a payment clears a trainee\'s full balance it appears here as a receipt.') ?></p>
                <?= $this->Html->link('<i class="fa fa-line-chart"></i> ' . __('Open Payment Tracking'),
                    ['action' => 'tracking'], ['escape' => false, 'class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.receipts-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.receipts-page .page-header h2 { margin: 0 0 5px 0; }
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
.summary-total   { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-settled { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-amount  { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-money { font-size: 20px; }
.summary-label { font-size: 13px; opacity: 0.95; }
.receipts-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 900px; margin: 0; }
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
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-success { background: #28a745; color: white; }
.badge-outline { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
</style>

<script>
(function () {
    document.querySelectorAll('.receipts-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>
