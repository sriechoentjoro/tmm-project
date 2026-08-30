<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CandidateSubmissionDocument $candidateSubmissionDocument
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Candidate Submission Document') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($candidateSubmissionDocument) ?>
    <fieldset>
        <?php
        echo $this->Form->control('applicant_id');
        echo $this->Form->control('document_id');
        echo $this->Form->control('submitted');
        echo $this->Form->control('submission_date', [
            'type' => 'text',
            'class' => 'form-control datepicker',
            'placeholder' => 'YYYY-MM-DD',
            'autocomplete' => 'off'
        ]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
