<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTrainingTestScoreGrade $masterTrainingTestScoreGrade
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Master Training Test Score Grade') ?> #<?= h($masterTrainingTestScoreGrade->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $masterTrainingTestScoreGrade['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($masterTrainingTestScoreGrade['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Title') ?></th><td style="padding: 8px 12px;"><?= h($masterTrainingTestScoreGrade['title']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Min Score') ?></th><td style="padding: 8px 12px;"><?= h($masterTrainingTestScoreGrade['min_score']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Max Score') ?></th><td style="padding: 8px 12px;"><?= h($masterTrainingTestScoreGrade['max_score']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Description') ?></th><td style="padding: 8px 12px;"><?= h($masterTrainingTestScoreGrade['description']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
