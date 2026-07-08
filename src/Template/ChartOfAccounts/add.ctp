<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChartOfAccount $chartOfAccount
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Chart Of Account') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($chartOfAccount) ?>
    <fieldset>
        <?php
        echo $this->Form->control('code');
        echo $this->Form->control('name');
        echo $this->Form->control('type');
        echo $this->Form->control('description');
        echo $this->Form->control('is_active');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
