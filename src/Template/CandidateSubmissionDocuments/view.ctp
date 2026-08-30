<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CandidateSubmissionDocument $candidateSubmissionDocument
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Candidate Submission Document') ?> #<?= h($candidateSubmissionDocument->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $candidateSubmissionDocument['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($candidateSubmissionDocument['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Applicant Id') ?></th><td style="padding: 8px 12px;"><?= h($candidateSubmissionDocument['applicant_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Document Id') ?></th><td style="padding: 8px 12px;"><?= h($candidateSubmissionDocument['document_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Submitted') ?></th><td style="padding: 8px 12px;"><?= h($candidateSubmissionDocument['submitted']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Submission Date') ?></th><td style="padding: 8px 12px;"><?= h($candidateSubmissionDocument['submission_date']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
