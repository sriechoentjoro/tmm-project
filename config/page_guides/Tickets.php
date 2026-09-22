<?php
/**
 * Guide for trainee tickets.
 */

return [
    'icon' => 'fa-ticket-alt',
    'title' => __('Trainee Tickets'),
    'subtitle' => __('The ticket bought for a trainee, the legs of the journey, and what it cost.'),
    'lead' => __('One ticket is one purchase; a journey to Japan is often two or three flights. The ticket carries the booking reference, the type, the status and the price; the legs hang off it, one row each with airline, flight number, the two airports and the two times. That is what makes a transit visible - two rows on one ticket, with a gap between the arrival of the first and the departure of the second, which the transit page works out for you.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Buys and records tickets, enters the legs, tracks what is paid.')],
        ['role' => 'tmm-accounting', 'can' => __('Reads the cost side.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The visa should be in hand: a ticket bought before it is a bet, not a booking.'), 'url' => '/trainee-record-coe-visas', 'label' => __('COE and Visa')],
        ['note' => __('Ticket types, statuses and currencies are master lists, so the summary counts can be trusted.'), 'url' => '/master-currencies', 'label' => __('Currencies')],
    ],

    'steps' => [
        [
            'title' => __('Record the ticket'),
            'who' => __('Documentation staff'),
            'do' => __('Choose the trainee, then enter the ticket number, the booking reference, the type, the status, and the price with its currency.'),
            'result' => __('The purchase is on file and counted in the summary by status.'),
            'screen' => ['/tickets/add', __('Add Ticket')],
            'data' => 'tickets',
        ],
        [
            'title' => __('Enter the legs'),
            'who' => __('Documentation staff'),
            'do' => __('One row per flight: airline, flight number, the airport it leaves from and the one it arrives at, with both times.'),
            'result' => __('The whole journey can be read at a glance.'),
            'screen' => ['/tickets/transit', __('Transit Routes')],
            'data' => 'trainee_flights',
        ],
        [
            'title' => __('Check the layover'),
            'who' => __('Documentation staff'),
            'do' => __('The transit page groups the legs by ticket and works out the gap between them.'),
            'result' => __('A connection that is too tight is caught before the day itself.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Visa received') . "] --> B[" . __('Ticket recorded') . "]\n"
        . "    B --> C[" . __('Leg 1') . "]\n"
        . "    B --> D[" . __('Leg 2, if any') . "]\n"
        . "    C --> E[" . __('Layover worked out') . "]\n"
        . "    D --> E\n"
        . "    B --> F[" . __('Counted in the cost summary') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-clipboard-check',
            'what' => __('The readiness grid ticks the ticket column as soon as a ticket exists for the trainee, whether or not it has any legs on it.'),
            'url' => '/trainee-documents/departure',
            'label' => __('Departure Readiness'),
        ],
        [
            'icon' => 'fa-money-bill-wave',
            'what' => __('The Ticket Costs entry in the menu opens this same list, with the same rows and the same totals. It is a second door, not a second page.'),
            'url' => '/tickets/costs',
            'label' => __('Ticket Costs'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Apprentice flights hang off a separate ticket register in its own database. Promotion copies nothing across, so a ticket recorded here is not the one an apprentice flight will find.'),
            'url' => '/apprentice-flights',
            'label' => __('Apprentice Flights'),
        ],
    ],

    'cautions' => [
        __('Nothing compares the times on two legs. Flights that overlap, or a connection of fifteen minutes, save without a word.'),
        __('The Departures entry in the menu does not open a page of its own - it sends you straight back to this list.'),
        __('A leg belongs to a ticket, not to a trainee. A leg on a ticket with no trainee on it is attached to nobody.'),
        __('The currency chosen here decides whether the price reaches the books. Accounting keeps its books in rupiah and nothing converts between currencies, so a ticket priced in yen is held back on the auto-generated journal page instead of being booked at its face value.'),
    ],
];
