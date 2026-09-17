<?php
/**
 * Add / edit form for a flight leg on an apprentice's ticket.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeFlight $apprenticeFlight
 * @var array|\Cake\ORM\Query $apprenticeTickets
 * @var array|\Cake\ORM\Query $masterAirlines
 * @var array|\Cake\ORM\Query $departureAirports
 * @var array|\Cake\ORM\Query $arrivalAirports
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $apprenticeFlight,
    'icon' => 'fa-plane',
    'title' => $isEdit ? __('Edit Flight') : __('Add Flight'),
    'subtitle' => __('One leg of a journey. A ticket with a transit has two.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Flight'),
    'sections' => [
        [
            'title' => __('Ticket and airline'),
            'icon' => 'fa-ticket-alt',
            'fields' => [
                'apprentice_ticket_id' => [
                    'label' => __('Ticket'),
                    'type' => 'select',
                    'options' => $apprenticeTickets,
                    'empty' => __('- choose a ticket -'),
                    'required' => true,
                    'help' => __('The ticket this leg belongs to.'),
                ],
                'master_airline_id' => [
                    'label' => __('Airline'),
                    'type' => 'select',
                    'options' => $masterAirlines,
                    'empty' => __('- choose an airline -'),
                    'help' => __('Who operates the flight.'),
                ],
                'flight_number' => [
                    'label' => __('Flight number'),
                    'type' => 'text',
                    'width' => 12,
                    'placeholder' => 'GA 874',
                    'help' => __('As printed on the ticket.'),
                ],
            ],
        ],
        [
            'title' => __('Route and times'),
            'icon' => 'fa-route',
            'fields' => [
                'departure_airport_id' => [
                    'label' => __('From'),
                    'type' => 'select',
                    'options' => $departureAirports,
                    'empty' => __('- choose an airport -'),
                    'help' => __('The airport this leg leaves from.'),
                ],
                'arrival_airport_id' => [
                    'label' => __('To'),
                    'type' => 'select',
                    'options' => $arrivalAirports,
                    'empty' => __('- choose an airport -'),
                    'help' => __('Where this leg lands.'),
                ],
                'departure_datetime' => [
                    'label' => __('Departure'),
                    'type' => 'datetime-local',
                    'help' => __('Local time at the departure airport.'),
                ],
                'arrival_datetime' => [
                    'label' => __('Arrival'),
                    'type' => 'datetime-local',
                    'help' => __('Local time at the arrival airport, which may be a day later.'),
                ],
            ],
        ],
    ],
]) ?>
