<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordCoeVisa $traineeRecordCoeVisa
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Record Coe Visa') ?> #<?= h($traineeRecordCoeVisa->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeRecordCoeVisa['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Trainee Coe Type Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['master_trainee_coe_type_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Date Coe Received') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['date_coe_received']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Date Visa Application') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['date_visa_application']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Date Visa Received') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['date_visa_received']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Place Visa Issued') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordCoeVisa['place_visa_issued']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
