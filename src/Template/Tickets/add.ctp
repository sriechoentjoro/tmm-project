<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ticket $ticket
 * @var array $traineeOptions
 * @var array $typeOptions
 * @var array $statusOptions
 * @var array $currencyOptions
 */
$this->assign('title', 'Add Ticket');

echo $this->element('Tickets/ticket_form', [
    'isEdit' => false,
    'linkedFlights' => [],
]);
