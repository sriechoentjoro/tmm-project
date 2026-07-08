<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeStory $apprenticeStory
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Apprentice Story') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($apprenticeStory) ?>
    <fieldset>
        <?php
        echo $this->Form->control('apprentice_id');
        echo $this->Form->control('title');
        echo $this->Form->control('date_occurrence');
        echo $this->Form->control('problem_contents');
        echo $this->Form->control('problem_classification');
        echo $this->Form->control('problem_solution');
        echo $this->Form->control('problem_inference');
        echo $this->Form->control('image_path');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
