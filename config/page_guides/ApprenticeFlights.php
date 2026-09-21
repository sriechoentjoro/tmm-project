<?php
/**
 * Guide for apprentice flights.
 */

return [
    'icon' => 'fa-plane-departure',
    'title' => __('Apprentice Flights'),
    'subtitle' => __('The legs of the journey to Japan, recorded against the ticket that was bought.'),
    'lead' => __('A ticket is one purchase; a journey is often two or three flights. This register keeps a row per leg - airline, flight number, the airport it leaves from and the one it arrives at, with the departure and arrival times - all hanging off the ticket rather than off the apprentice directly. That is what makes a transit visible: two rows on one ticket, with a gap between the arrival of the first and the departure of the second.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records the legs once the ticket is issued.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The visa has to be in hand: the date on the ticket is only real once the person may actually travel.'), 'url' => '/apprentice-record-coe-visas', 'label' => __('COE and Visa')],
        ['note' => __('Airlines and airports come from master lists, so a flight names the same airport every time. Those two lists have no page of their own - an airline or an airport that is not already in the table cannot be added from anywhere in the application.')],
    ],

    'steps' => [
        [
            'title' => __('Record each leg'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the ticket, then enter the airline, the flight number, the two airports and the two times, one row per leg.'),
            'result' => __('The whole journey is on file and can be read at a glance.'),
            'screen' => ['/apprentice-flights/add', __('Add Flight')],
            'data' => 'apprentice_flights',
        ],
        [
            'title' => __('Check the transit'),
            'who' => __('Apprentice staff'),
            'do' => __('With both legs entered, compare the arrival time of the first against the departure of the second.'),
            'result' => __('A connection that is too tight is caught before the day itself.'),
            'note' => __('Nothing compares the times for you. Two legs that overlap, or a connection of fifteen minutes, will be saved without a word.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Visa received') . "] --> B[" . __('Ticket issued') . "]\n"
        . "    B --> C[" . __('Leg 1 recorded') . "]\n"
        . "    B --> D[" . __('Leg 2 recorded, if any') . "]\n"
        . "    C --> E[" . __('Departure') . "]\n"
        . "    D --> E\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-stamp',
            'what' => __('The visa date is what makes a booking safe to make.'),
            'url' => '/apprentice-record-coe-visas',
            'label' => __('COE and Visa'),
        ],
        [
            'icon' => 'fa-ticket-alt',
            'what' => __('Flights hang off a ticket, and apprentice tickets are kept in their own database, separate from the trainee ticketing used earlier in the pipeline. The two are not the same list.'),
            'url' => '/tickets',
            'label' => __('Tickets'),
        ],
    ],

    'cautions' => [
        __('A flight is attached to a ticket, not to an apprentice. To know whose flight it is you follow the ticket, and a ticket with no apprentice on it makes the leg an orphan.'),
        __('Recording a departure changes nothing on the apprentice record. The flag the reports read as "in Japan" is not set here, or anywhere else automatically.'),
    ],
];
