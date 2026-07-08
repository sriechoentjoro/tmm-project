<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LpkPhysicalTest $lpkPhysicalTest
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Lpk Physical Test') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($lpkPhysicalTest) ?>
    <fieldset>
        <?php
        echo $this->Form->control('candidate_id');
        echo $this->Form->control('placement_test_set_id');
        echo $this->Form->control('test_date');
        echo $this->Form->control('score');
        echo $this->Form->control('total_points');
        echo $this->Form->control('passing_score');
        echo $this->Form->control('is_passed');
        echo $this->Form->control('test_duration_minutes');
        echo $this->Form->control('started_at');
        echo $this->Form->control('completed_at');
        echo $this->Form->control('notes');
        echo $this->Form->control('graded_by');
        echo $this->Form->control('graded_at');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
