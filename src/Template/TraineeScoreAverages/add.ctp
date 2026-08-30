<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeScoreAverage $traineeScoreAverage
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Trainee Score Average') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($traineeScoreAverage) ?>
    <fieldset>
        <?php
        echo $this->Form->control('trainee_id');
        echo $this->Form->control('master_training_competency_id');
        echo $this->Form->control('score_average');
        echo $this->Form->control('master_training_test_score_grade_id');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
