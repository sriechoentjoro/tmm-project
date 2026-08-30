<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterCurrency $masterCurrency
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Master Currency') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($masterCurrency) ?>
    <fieldset>
        <?php
        echo $this->Form->control('title');
        echo $this->Form->control('currency_code');
        echo $this->Form->control('country');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
