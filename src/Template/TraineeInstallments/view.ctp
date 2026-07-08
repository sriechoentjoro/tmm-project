<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment $traineeInstallment
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Installment') ?> #<?= h($traineeInstallment->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeInstallment['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Transaction Category Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['master_transaction_category_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Payment Amount') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['payment_amount']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Payment Date') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['payment_date']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Full Payment Amount') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['full_payment_amount']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Payment Accummulated') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['payment_accummulated']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Unpaid Amount') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['unpaid_amount']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Currency Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['master_currency_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Is Paid Off') ?></th><td style="padding: 8px 12px;"><?= h($traineeInstallment['is_paid_off']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
