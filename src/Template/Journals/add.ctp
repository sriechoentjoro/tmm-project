<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Journal $journal
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Add Journal') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($journal) ?>
    <fieldset>
        <?php
        echo $this->Form->control('transaction_date');
        echo $this->Form->control('reference_no');
        echo $this->Form->control('description');
        echo $this->Form->control('total_debit');
        echo $this->Form->control('total_credit');
        echo $this->Form->control('status');
        echo $this->Form->control('created_by');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
