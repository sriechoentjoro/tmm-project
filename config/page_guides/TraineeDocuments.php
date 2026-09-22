<?php
/**
 * Guide for the trainee departure-document hub.
 */

return [
    'icon' => 'fa-clipboard-check',
    'title' => __('Trainee Departure Documents'),
    'subtitle' => __('Where a trainee stands across the four things needed before leaving.'),
    'lead' => __('This is not a register of its own; it is the view over four of them. A trainee cannot leave without a passport, a certificate of eligibility with its visa, a current medical check-up, and a ticket, and each of those lives in its own screen. The readiness page puts all four in one row per trainee so the question "who is still missing something" has an answer without opening anybody. The ticketing page does the same for the three ticket screens.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Collects the papers and keeps all four registers current.')],
        ['role' => 'tmm-training', 'can' => __('Reads it: the same four things decide whether somebody is ready to be promoted and sent.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The person has to be a trainee first, which happens by being promoted from a candidate.'), 'url' => '/trainees', 'label' => __('Trainees')],
    ],

    'steps' => [
        [
            'title' => __('Read the readiness grid'),
            'who' => __('Documentation staff'),
            'do' => __('One row per trainee, one tick per requirement, and a count of how many have all four.'),
            'result' => __('The gap is visible at a glance instead of being discovered on the day.'),
            'screen' => ['/trainee-documents/departure', __('Departure Readiness')],
            'note' => __('A tick means a row exists, not that the row is complete. A passport record with no dates on it still ticks the box.'),
        ],
        [
            'title' => __('Work the register that is short'),
            'who' => __('Documentation staff'),
            'do' => __('Each column links to the screen behind it: passports, certificate and visa, medical check-ups, tickets.'),
            'result' => __('The missing paper is recorded where it belongs.'),
            'screen' => ['/trainee-record-pasports', __('Passports')],
        ],
        [
            'title' => __('The ticketing side'),
            'who' => __('Documentation staff'),
            'do' => __('Tickets, the flight legs behind them, and what they cost each have a screen; this page gathers the three.'),
            'result' => __('The journey and its price are on file.'),
            'screen' => ['/trainee-documents/ticketing', __('Ticketing')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Passport') . "] --> E{" . __('Readiness grid') . "}\n"
        . "    B[" . __('COE and visa') . "] --> E\n"
        . "    C[" . __('Medical check-up') . "] --> E\n"
        . "    D[" . __('Ticket') . "] --> E\n"
        . "    E --> F[" . __('All four: ready to go') . "]\n"
        . "    E --> G[" . __('Anything missing: chase it') . "]\n"
        . "    style E fill:#e3f2fd\n"
        . "    style F fill:#c8e6c9\n"
        . "    style G fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Promotion to apprentice does not carry any of these documents across. The apprentice side starts with an empty trail and the same papers are recorded again there, in their own registers.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('What TMM Training weighs before recording a departure is the apprentice-side documents, not these. These are what gets a trainee to the point of being promoted at all.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
    ],

    'cautions' => [
        __('Readiness here counts rows, not contents. Somebody who is "ready" by this grid may still have a passport with no expiry recorded and a ticket with no flight legs.'),
        __('The grid has no idea whether a medical check-up passed. It counts that one was recorded.'),
        __('Nothing on this page writes anything. It is a view; every correction is made in the register it points at.'),
    ],
];
