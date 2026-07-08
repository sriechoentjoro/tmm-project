<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment $traineeInstallment
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Trainee Installment') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($traineeInstallment) ?>
    <fieldset>
        <?php
        echo $this->Form->control('trainee_id');
        echo $this->Form->control('master_transaction_category_id');
        echo $this->Form->control('payment_amount');
        echo $this->Form->control('payment_date');
        echo $this->Form->control('full_payment_amount');
        echo $this->Form->control('payment_accummulated');
        echo $this->Form->control('unpaid_amount');
        echo $this->Form->control('master_currency_id');
        echo $this->Form->control('is_paid_off');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
