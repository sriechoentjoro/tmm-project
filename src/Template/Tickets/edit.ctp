<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ticket $ticket
 * @var array $traineeOptions
 * @var array $typeOptions
 * @var array $statusOptions
 * @var array $currencyOptions
 * @var array $linkedFlights
 */
$this->assign('title', 'Edit Ticket #' . $ticket->id);

echo $this->element('Tickets/ticket_form', [
    'isEdit' => true,
    'linkedFlights' => isset($linkedFlights) ? $linkedFlights : [],
]);
