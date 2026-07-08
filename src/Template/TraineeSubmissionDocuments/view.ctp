<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeSubmissionDocument $traineeSubmissionDocument
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Submission Document') ?> #<?= h($traineeSubmissionDocument->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeSubmissionDocument['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Apprenticeship Submission Document Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['apprenticeship_submission_document_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('File Path') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['file_path']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Document Submission Status Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['master_document_submission_status_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Uploaded By') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['uploaded_by']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Uploaded At') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['uploaded_at']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Notes') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['notes']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Created') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['created']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Modified') ?></th><td style="padding: 8px 12px;"><?= h($traineeSubmissionDocument['modified']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
