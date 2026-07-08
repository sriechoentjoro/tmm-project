<?php
/**
 * Shared passport record form (add + edit)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordPasport $traineeRecordPasport
 * @var array $traineeOptions      [id => "Name (TMM-code)"]
 * @var int $preselectedTrainee    (add only) trainee from ?trainee_id=
 * @var bool $isEdit
 */
$isEdit = !empty($isEdit);
$preselectedTrainee = isset($preselectedTrainee) ? (int)$preselectedTrainee : 0;

// Keep a reference to a trainee that no longer exists so editing does not drop it
if ($isEdit && $traineeRecordPasport->trainee_id && !isset($traineeOptions[$traineeRecordPasport->trainee_id])) {
    $traineeOptions[$traineeRecordPasport->trainee_id] = '#' . $traineeRecordPasport->trainee_id . ' (' . __('unknown trainee') . ')';
}

// Native date input (Cake's own date control renders y/m/d dropdowns)
$dateField = function ($field, $label) use ($traineeRecordPasport) {
    $value = $traineeRecordPasport->get($field);
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

<div class="passport-form-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fa fa-id-card-o"></i>
                <?= $isEdit ? __('Edit Passport Record') . ' <span class="record-id">#' . h($traineeRecordPasport->id) . '</span>' : __('Add Passport Record') ?>
            </h2>
            <p class="text-muted"><?= __('Passport administration for a trainee going to Japan') ?> 🇯🇵</p>
        </div>
        <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('Back to List'),
            ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-pencil-square-o"></i> <?= __('Passport Details') ?></h4></div>
                <div class="card-body">
                    <?php if (!$isEdit && $preselectedTrainee && isset($traineeOptions[$preselectedTrainee])): ?>
                        <div class="preselect-banner">
                            <i class="fa fa-user-circle-o"></i>
                            <?= __('Adding passport record for') ?>
                            <strong><?= h($traineeOptions[$preselectedTrainee]) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->create($traineeRecordPasport) ?>

                    <?= $this->Form->control('trainee_id', [
                        'label' => __('Trainee'),
                        'options' => $traineeOptions,
                        'empty' => '— ' . __('Select trainee') . ' —',
                        'required' => true,
                        'default' => $preselectedTrainee ?: null,
                    ]) ?>

                    <hr class="field-divider">

                    <div class="field-grid">
                        <?= $dateField('date_issue', __('Date of Issue')) ?>
                        <?= $this->Form->control('place_issue', [
                            'label' => __('Place of Issue'),
                            'placeholder' => 'e.g. Jakarta Selatan',
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="field-grid">
                        <?= $dateField('date_received', __('Date Received (by TMM)')) ?>
                        <?= $dateField('date_paid', __('Date Paid')) ?>
                    </div>

                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fa fa-save"></i> ' . __('Save Record'),
                            ['escapeTitle' => false, 'class' => 'btn btn-success btn-lg']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'],
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?php if ($isEdit): ?>
                            <span class="actions-spacer"></span>
                            <?= $this->Form->postLink('<i class="fa fa-trash"></i> ' . __('Delete'),
                                ['action' => 'delete', $traineeRecordPasport->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-outline-danger btn-lg',
                                    'confirm' => __('Delete passport record #{0}? This cannot be undone.', $traineeRecordPasport->id),
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-info-circle"></i> <?= __('Field Guide') ?></h4></div>
                <div class="card-body">
                    <ul class="guide-list">
                        <li><strong><?= __('Date of Issue') ?></strong> — <?= __('when the immigration office issued the passport') ?></li>
                        <li><strong><?= __('Place of Issue') ?></strong> — <?= __('the immigration office listed in the passport') ?></li>
                        <li><strong><?= __('Date Received') ?></strong> — <?= __('when TMM received the physical passport from the trainee') ?></li>
                        <li><strong><?= __('Date Paid') ?></strong> — <?= __('when the passport fee was settled') ?></li>
                    </ul>
                    <p class="text-muted" style="font-size: 0.85em; margin-top: 12px;">
                        <i class="fa fa-lightbulb-o"></i>
                        <?= __('One record per trainee is enough - it marks the passport step complete in the pre-departure pipeline.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.passport-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.passport-form-page .page-header h2 { margin: 0 0 5px 0; }
.record-id { color: #6c757d; font-weight: normal; }
.passport-form-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.passport-form-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.passport-form-page .card-header h4 { margin: 0; }
.passport-form-page .card-body { padding: 20px; }
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 15px;
}
@media (max-width: 640px) {
    .field-grid { grid-template-columns: 1fr; }
}
.passport-form-page .input { margin-bottom: 18px; }
.passport-form-page label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #495057;
    font-size: 0.95em;
}
.passport-form-page .input input[type="text"],
.passport-form-page .input input[type="date"],
.passport-form-page .input select {
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
.passport-form-page .input input:focus,
.passport-form-page .input select:focus {
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
.guide-list { list-style: none; padding: 0; margin: 0; }
.guide-list li { padding: 6px 0; color: #495057; }
</style>
