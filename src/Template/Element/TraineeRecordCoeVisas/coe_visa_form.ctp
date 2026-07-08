<?php
/**
 * Shared COE/visa record form (add + edit)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordCoeVisa $traineeRecordCoeVisa
 * @var array $traineeOptions   [id => "Name (TMM-code)"]
 * @var array $coeTypeOptions   [id => title]
 * @var int $preselectedTrainee (add only) trainee from ?trainee_id=
 * @var bool $isEdit
 */
$isEdit = !empty($isEdit);
$preselectedTrainee = isset($preselectedTrainee) ? (int)$preselectedTrainee : 0;

// Keep a reference to a trainee that no longer exists so editing does not drop it
if ($isEdit && $traineeRecordCoeVisa->trainee_id && !isset($traineeOptions[$traineeRecordCoeVisa->trainee_id])) {
    $traineeOptions[$traineeRecordCoeVisa->trainee_id] = '#' . $traineeRecordCoeVisa->trainee_id . ' (' . __('unknown trainee') . ')';
}

// Native date input (Cake's own date control renders y/m/d dropdowns)
$dateField = function ($field, $label) use ($traineeRecordCoeVisa) {
    $value = $traineeRecordCoeVisa->get($field);
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        $value = $value->format('Y-m-d');
    }
    $id = str_replace('_', '-', $field);
    return '<div class="input date required">'
        . '<label for="' . $id . '">' . $label . '</label>'
        . '<input type="date" name="' . $field . '" id="' . $id . '" required value="' . h((string)$value) . '">'
        . '</div>';
};
?>

<div class="coevisa-form-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fa fa-file-text-o"></i>
                <?= $isEdit ? __('Edit COE / Visa Record') . ' <span class="record-id">#' . h($traineeRecordCoeVisa->id) . '</span>' : __('Add COE / Visa Record') ?>
            </h2>
            <p class="text-muted"><?= __('Certificate of Eligibility and visa administration for a trainee going to Japan') ?> 🇯🇵</p>
        </div>
        <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
            ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-pencil-square-o"></i> <?= __('COE & Visa Details') ?></h4></div>
                <div class="card-body">
                    <?php if (!$isEdit && $preselectedTrainee && isset($traineeOptions[$preselectedTrainee])): ?>
                        <div class="preselect-banner">
                            <i class="fa fa-user-circle-o"></i>
                            <?= __('Adding COE / visa record for') ?>
                            <strong><?= h($traineeOptions[$preselectedTrainee]) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->create($traineeRecordCoeVisa) ?>

                    <div class="field-grid">
                        <?= $this->Form->control('trainee_id', [
                            'label' => __('Trainee'),
                            'options' => $traineeOptions,
                            'empty' => '— ' . __('Select trainee') . ' —',
                            'required' => true,
                            'default' => $preselectedTrainee ?: null,
                        ]) ?>
                        <?= $this->Form->control('master_trainee_coe_type_id', [
                            'label' => __('COE Type'),
                            'options' => $coeTypeOptions,
                            'empty' => '— ' . __('Select COE type') . ' —',
                            'required' => true,
                        ]) ?>
                    </div>

                    <hr class="field-divider">

                    <div class="field-grid">
                        <?= $dateField('date_coe_received', __('COE Received')) ?>
                        <?= $dateField('date_visa_application', __('Visa Application')) ?>
                    </div>

                    <div class="field-grid">
                        <?= $dateField('date_visa_received', __('Visa Received')) ?>
                        <?= $this->Form->control('place_visa_issued', [
                            'label' => __('Visa Issued In'),
                            'placeholder' => 'e.g. Jakarta',
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Record'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?php if ($isEdit): ?>
                            <span class="actions-spacer"></span>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                                ['action' => 'delete', $traineeRecordCoeVisa->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-outline-danger btn-lg',
                                    'confirm' => __('Delete COE / visa record #{0}? This cannot be undone.', $traineeRecordCoeVisa->id),
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('Process Guide') ?></h4></div>
                <div class="card-body">
                    <ol class="guide-steps">
                        <li>
                            <strong><?= __('COE Received') ?></strong>
                            <span><?= __('Japan Immigration issues the Certificate of Eligibility and TMM receives it') ?></span>
                        </li>
                        <li>
                            <strong><?= __('Visa Application') ?></strong>
                            <span><?= __('The visa application is submitted to the Japanese embassy / consulate with the COE') ?></span>
                        </li>
                        <li>
                            <strong><?= __('Visa Received') ?></strong>
                            <span><?= __('The passport comes back with the visa - record where it was issued') ?></span>
                        </li>
                    </ol>
                    <p class="text-muted" style="font-size: 0.85em; margin-top: 12px;">
                        <i class="fa fa-lightbulb-o"></i>
                        <?= __('COE Biasa = paper certificate, COE Electronic = e-COE. One record per trainee marks the COE/visa step complete in the pre-departure pipeline.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.coevisa-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.coevisa-form-page .page-header h2 { margin: 0 0 5px 0; }
.record-id { color: #6c757d; font-weight: normal; }
.coevisa-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.coevisa-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.coevisa-form-page .card-header h4 { margin: 0; }
.coevisa-form-page .card-body { padding: 20px; }
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 15px;
}
@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}
.coevisa-form-page .input { margin-bottom: 18px; }
.coevisa-form-page label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #495057;
    font-size: 0.95em;
}
.coevisa-form-page .input input[type="text"],
.coevisa-form-page .input input[type="date"],
.coevisa-form-page .input select {
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
.coevisa-form-page .input input:focus,
.coevisa-form-page .input select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
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
.guide-steps { padding-left: 20px; margin: 0; }
.guide-steps li { padding: 6px 0; color: #495057; }
.guide-steps li strong { display: block; }
.guide-steps li span { font-size: 0.9em; color: #6c757d; }
</style>
