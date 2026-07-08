<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeTrainingTestScore $traineeTrainingTestScore
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Training Test Score') ?> #<?= h($traineeTrainingTestScore->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeTrainingTestScore['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Training Competency Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['master_training_competency_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Test Date') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['test_date']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Score') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['score']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Training Test Score Grade Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeTrainingTestScore['master_training_test_score_grade_id']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
