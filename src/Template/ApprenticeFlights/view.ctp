<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeFlight $apprenticeFlight
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Apprentice Flight') ?> #<?= h($apprenticeFlight->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $apprenticeFlight['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Apprentice Ticket Id') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['apprentice_ticket_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Master Airline Id') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['master_airline_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Flight Number') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['flight_number']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Departure Airport Id') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['departure_airport_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Arrival Airport Id') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['arrival_airport_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Departure Datetime') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['departure_datetime']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Arrival Datetime') ?></th><td style="padding: 8px 12px;"><?= h($apprenticeFlight['arrival_datetime']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>
