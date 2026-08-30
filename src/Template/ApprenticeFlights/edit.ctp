<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeFlight $apprenticeFlight
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Apprentice Flight') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($apprenticeFlight) ?>
    <fieldset>
        <?php
        echo $this->Form->control('apprentice_ticket_id');
        echo $this->Form->control('master_airline_id');
        echo $this->Form->control('flight_number');
        echo $this->Form->control('departure_airport_id');
        echo $this->Form->control('arrival_airport_id');
        echo $this->Form->control('departure_datetime', ['type' => 'datetime-local', 'class' => 'form-control']);
        echo $this->Form->control('arrival_datetime', ['type' => 'datetime-local', 'class' => 'form-control']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
