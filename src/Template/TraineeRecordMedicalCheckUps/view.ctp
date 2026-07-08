<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordMedicalCheckUp $traineeRecordMedicalCheckUp
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Record Medical Check Up') ?> #<?= h($traineeRecordMedicalCheckUp->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeRecordMedicalCheckUp['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Title') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['title']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Date Issued') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['date_issued']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Medical Check Up Result Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['master_medical_check_up_result_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Comment') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['comment']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Clinic') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['clinic']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Mcu Files') ?></th><td style="padding: 8px 12px;"><?= h($traineeRecordMedicalCheckUp['mcu_files']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
