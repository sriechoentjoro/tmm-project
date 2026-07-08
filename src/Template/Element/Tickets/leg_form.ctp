<?php
/**
 * Flight leg form - add or edit (shared by transit page + ticket edit sidebar)
 *
 * @var \App\View\AppView $this
 * @var int $ticketId
 * @var array|null $leg              existing leg row => edit mode, null => add mode
 * @var int $redirectTicket          when set, the controller redirects back to the ticket edit page
 * @var string $defaultDeparture     datetime-local prefill for add mode
 * @var array $airlineOptions        (view var) [id => "Name (CODE)"]
 * @var array $airportOptions        (view var) [id => "CODE — City, Country"]
 */
$leg = isset($leg) ? $leg : null;
$redirectTicket = isset($redirectTicket) ? (int)$redirectTicket : 0;
$defaultDeparture = isset($defaultDeparture) ? $defaultDeparture : '';
$isEditLeg = !empty($leg);

$toDatetimeLocal = function ($value) {
    $timestamp = $value ? strtotime($value) : false;
    return $timestamp ? date('Y-m-d\TH:i', $timestamp) : '';
};
?>
<?= $this->Form->create(null, ['url' => ['action' => 'transit'], 'class' => 'add-leg-form']) ?>
<?= $this->Form->hidden('form_action', ['value' => $isEditLeg ? 'edit_leg' : 'add_leg', 'id' => false]) ?>
<?= $this->Form->hidden('ticket_id', ['value' => $ticketId, 'id' => false]) ?>
<?php if ($isEditLeg): ?>
    <?= $this->Form->hidden('flight_id', ['value' => $leg['id'], 'id' => false]) ?>
<?php endif; ?>
<?php if ($redirectTicket): ?>
    <?= $this->Form->hidden('redirect_ticket', ['value' => $redirectTicket, 'id' => false]) ?>
<?php endif; ?>
<div class="leg-form-grid">
    <div class="leg-field">
        <label><?= __('Airline') ?></label>
        <?= $this->Form->select('master_airline_id', $airlineOptions, [
            'empty' => '— ' . __('Airline') . ' —',
            'required' => true,
            'value' => $isEditLeg ? $leg['master_airline_id'] : null,
        ]) ?>
    </div>
    <div class="leg-field">
        <label><?= __('Flight Number') ?></label>
        <?= $this->Form->text('flight_number', [
            'placeholder' => 'e.g. GA888',
            'required' => true,
            'value' => $isEditLeg ? $leg['flight_number'] : '',
        ]) ?>
    </div>
    <div class="leg-field">
        <label><?= __('From') ?></label>
        <?= $this->Form->select('departure_airport_id', $airportOptions, [
            'empty' => '— ' . __('Departure airport') . ' —',
            'required' => true,
            'value' => $isEditLeg ? $leg['departure_airport_id'] : null,
        ]) ?>
    </div>
    <div class="leg-field">
        <label><?= __('To') ?></label>
        <?= $this->Form->select('arrival_airport_id', $airportOptions, [
            'empty' => '— ' . __('Arrival airport') . ' —',
            'required' => true,
            'value' => $isEditLeg ? $leg['arrival_airport_id'] : null,
        ]) ?>
    </div>
    <div class="leg-field">
        <label><?= __('Departure') ?></label>
        <input type="datetime-local" name="departure_datetime"
               value="<?= h($isEditLeg ? $toDatetimeLocal($leg['departure_datetime']) : $defaultDeparture) ?>" required>
    </div>
    <div class="leg-field">
        <label><?= __('Arrival') ?></label>
        <input type="datetime-local" name="arrival_datetime"
               value="<?= h($isEditLeg ? $toDatetimeLocal($leg['arrival_datetime']) : '') ?>" required>
    </div>
    <div class="leg-field leg-field-submit">
        <label>&nbsp;</label>
        <?= $this->Form->button(
            $isEditLeg
                ? '<i class="fa fa-save"></i> ' . __('Save Changes')
                : '<i class="fa fa-plus"></i> ' . __('Add Leg'),
            ['escapeTitle' => false, 'class' => 'btn btn-sm ' . ($isEditLeg ? 'btn-primary' : 'btn-success')]) ?>
    </div>
</div>
<?= $this->Form->end() ?>
