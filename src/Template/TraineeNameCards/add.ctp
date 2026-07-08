<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeNameCard $traineeNameCard
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Trainee Name Card') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($traineeNameCard) ?>
    <fieldset>
        <?php
        echo $this->Form->control('trainee_id');
        echo $this->Form->control('issue_date');
        echo $this->Form->control('valid_until');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
