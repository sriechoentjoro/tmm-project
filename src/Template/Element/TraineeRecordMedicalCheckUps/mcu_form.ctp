<?php
/**
 * Shared medical check-up form (add + edit)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordMedicalCheckUp $traineeRecordMedicalCheckUp
 * @var array $traineeOptions   [id => "Name (TMM-code)"]
 * @var array $resultOptions    [id => title]
 * @var int $preselectedTrainee (add only) trainee from ?trainee_id=
 * @var bool $isEdit
 */
$isEdit = !empty($isEdit);
$preselectedTrainee = isset($preselectedTrainee) ? (int)$preselectedTrainee : 0;

// Keep a reference to a trainee that no longer exists so editing does not drop it
if ($isEdit && $traineeRecordMedicalCheckUp->trainee_id && !isset($traineeOptions[$traineeRecordMedicalCheckUp->trainee_id])) {
    $traineeOptions[$traineeRecordMedicalCheckUp->trainee_id] = '#' . $traineeRecordMedicalCheckUp->trainee_id . ' (' . __('unknown trainee') . ')';
}

$dateValue = $traineeRecordMedicalCheckUp->date_issued;
if ($dateValue instanceof \DateTimeInterface || $dateValue instanceof \Cake\I18n\Date) {
    $dateValue = $dateValue->format('Y-m-d');
}
$currentFile = $traineeRecordMedicalCheckUp->mcu_files
    ? str_replace('\\', '/', $traineeRecordMedicalCheckUp->mcu_files)
    : null;
?>

<div class="mcu-form-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fa fa-heartbeat"></i>
                <?= $isEdit ? __('Edit Medical Check-Up') . ' <span class="record-id">#' . h($traineeRecordMedicalCheckUp->id) . '</span>' : __('Add Medical Check-Up') ?>
            </h2>
            <p class="text-muted"><?= __('Health certificate for a trainee going to Japan') ?> 🇯🇵</p>
        </div>
        <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
            ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-pencil-square-o"></i> <?= __('Check-Up Details') ?></h4></div>
                <div class="card-body">
                    <?php if (!$isEdit && $preselectedTrainee && isset($traineeOptions[$preselectedTrainee])): ?>
                        <div class="preselect-banner">
                            <i class="fa fa-user-circle-o"></i>
                            <?= __('Adding medical check-up for') ?>
                            <strong><?= h($traineeOptions[$preselectedTrainee]) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->create($traineeRecordMedicalCheckUp, ['type' => 'file']) ?>

                    <div class="field-grid">
                        <?= $this->Form->control('trainee_id', [
                            'label' => __('Trainee'),
                            'options' => $traineeOptions,
                            'empty' => '— ' . __('Select trainee') . ' —',
                            'required' => true,
                            'default' => $preselectedTrainee ?: null,
                        ]) ?>
                        <?= $this->Form->control('title', [
                            'label' => __('Check-Up Title'),
                            'placeholder' => 'e.g. Medical check up pertama',
                            'required' => true,
                        ]) ?>
                    </div>

                    <hr class="field-divider">

                    <div class="field-grid">
                        <?= $this->Form->control('master_medical_check_up_result_id', [
                            'label' => __('Result'),
                            'options' => $resultOptions,
                            'empty' => '— ' . __('Select result') . ' —',
                            'required' => true,
                        ]) ?>
                        <?= $this->Form->control('clinic', [
                            'label' => __('Clinic / Hospital'),
                            'placeholder' => 'e.g. Kimia Farma',
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="field-grid">
                        <div class="input date required">
                            <label for="date-issued"><?= __('Date Issued') ?></label>
                            <input type="date" name="date_issued" id="date-issued" required value="<?= h((string)$dateValue) ?>">
                        </div>
                        <div class="input file">
                            <label for="mcu-file"><?= __('MCU Report File') ?><?= $isEdit ? ' (' . __('choose to replace') . ')' : '' ?></label>
                            <input type="file" name="mcu_file_upload" id="mcu-file"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <?php if ($currentFile): ?>
                                <small class="current-file">
                                    <i class="fa fa-paperclip"></i> <?= __('Current file:') ?>
                                    <a href="<?= $this->Url->build('/' . ltrim($currentFile, '/')) ?>" target="_blank"><?= h(basename($currentFile)) ?></a>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?= $this->Form->control('comment', [
                        'label' => __('Doctor\'s Notes') . ' (' . __('optional') . ')',
                        'type' => 'textarea',
                        'rows' => 3,
                    ]) ?>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Record'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?php if ($isEdit): ?>
                            <span class="actions-spacer"></span>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                                ['action' => 'delete', $traineeRecordMedicalCheckUp->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-outline-danger btn-lg',
                                    'confirm' => __('Delete medical check-up #{0}? This cannot be undone.', $traineeRecordMedicalCheckUp->id),
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('Result Guide') ?></h4></div>
                <div class="card-body">
                    <ul class="guide-list">
                        <li><span class="badge badge-success"><?= __('Fit') ?></span> <?= __('Healthy - cleared for departure') ?></li>
                        <li><span class="badge badge-warning"><?= __('Fit dengan keterangan') ?></span> <?= __('Cleared with medical notes - read the doctor\'s comments') ?></li>
                        <li><span class="badge badge-danger"><?= __('Tidak Fit') ?></span> <?= __('Not cleared - follow-up check-up required') ?></li>
                    </ul>
                    <p class="text-muted" style="font-size: 0.85em; margin-top: 12px;">
                        <i class="fa fa-lightbulb-o"></i>
                        <?= __('A trainee can have multiple check-ups (e.g. first and follow-up). Attach the clinic\'s report as PDF or photo. Allowed: PDF, JPG, PNG, DOC(X).') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mcu-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.mcu-form-page .page-header h2 { margin: 0 0 5px 0; }
.record-id { color: #6c757d; font-weight: normal; }
.mcu-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.mcu-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.mcu-form-page .card-header h4 { margin: 0; }
.mcu-form-page .card-body { padding: 20px; }
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 15px;
}
@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}
.mcu-form-page .input { margin-bottom: 18px; }
.mcu-form-page label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #495057;
    font-size: 0.95em;
}
.mcu-form-page .input input[type="text"],
.mcu-form-page .input input[type="date"],
.mcu-form-page .input select,
.mcu-form-page .input textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #fff;
    font-size: 15px;
    font-family: inherit;
    color: #333;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}
.mcu-form-page .input input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px dashed #ced4da;
    border-radius: 8px;
    background: #fafbff;
    box-sizing: border-box;
}
.mcu-form-page .input input:focus,
.mcu-form-page .input select:focus,
.mcu-form-page .input textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
.current-file { display: block; margin-top: 6px; color: #6c757d; }
.current-file a { color: #667eea; }
.preselect-banner {
    padding: 10px 15px;
    margin-bottom: 18px;
    background: #eef1ff;
    border: 1px solid #c7cffb;
    border-left: 4px solid #667eea;
    border-radius: 8px;
    color: #3b4890;
}
.preselect-banner i { margin-right: 6px; }
.field-divider {
    border: none;
    border-top: 1px dashed #dee2e6;
    margin: 5px 0 18px 0;
}
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    align-items: center;
    flex-wrap: wrap;
}
.actions-spacer { flex: 1; }
.guide-list { list-style: none; padding: 0; margin: 0; }
.guide-list li { padding: 8px 0; color: #495057; }
.guide-list .badge { margin-right: 8px; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-success { background: #28a745; color: white; }
.badge-danger { background: #dc3545; color: white; }
.badge-warning { background: #ffc107; color: #333; }
</style>
