<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LpkPhysicalTest $lpkPhysicalTest
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Lpk Physical Test') ?> #<?= h($lpkPhysicalTest->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $lpkPhysicalTest['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Candidate Id') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['candidate_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Placement Test Set Id') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['placement_test_set_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Test Date') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['test_date']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Score') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['score']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Total Points') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['total_points']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Passing Score') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['passing_score']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Is Passed') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['is_passed']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Test Duration Minutes') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['test_duration_minutes']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Started At') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['started_at']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Completed At') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['completed_at']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Notes') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['notes']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Graded By') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['graded_by']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Graded At') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['graded_at']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Created') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['created']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Modified') ?></th><td style="padding: 8px 12px;"><?= h($lpkPhysicalTest['modified']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
