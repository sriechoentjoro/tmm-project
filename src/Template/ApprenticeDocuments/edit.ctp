<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeDocument $apprenticeDocument
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Apprentice Document') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($apprenticeDocument) ?>
    <fieldset>
        <?php
        echo $this->Form->control('apprentice_id');
        echo $this->Form->control('apprenticeship_submission_document_id');
        echo $this->Form->control('file_path');
        echo $this->Form->control('master_document_submission_status_id');
        echo $this->Form->control('uploaded_by');
        echo $this->Form->control('uploaded_at', [
            'type' => 'text',
            'class' => 'form-control datepicker',
            'placeholder' => 'YYYY-MM-DD',
            'autocomplete' => 'off'
        ]);
        echo $this->Form->control('notes');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
