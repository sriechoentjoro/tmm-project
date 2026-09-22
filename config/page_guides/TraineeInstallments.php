<?php
/**
 * Guide for trainee installments.
 */

return [
    'icon' => 'fa-file-invoice-dollar',
    'title' => __('Trainee Installments'),
    'subtitle' => __('What a trainee owes, what they have paid, and what is left.'),
    'lead' => __('A trainee is given one opening figure - the whole cost they owe - and then a row for every payment that comes in. Each payment row is worked out from the one before it: the accumulated total goes up, the unpaid balance comes down, and when the balance reaches zero the row is marked settled and appears on the receipts page. Nothing is typed twice and no running total is typed at all, so the balance cannot drift from the payments behind it.'),

    'actors' => [
        ['role' => 'tmm-accounting', 'can' => __('Sets the owing cost, records payments, reads the tracking.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The trainee has to exist, which happens by being promoted from a candidate.'), 'url' => '/trainees', 'label' => __('Trainees')],
    ],

    'steps' => [
        [
            'title' => __('Set what they owe'),
            'who' => __('Accounting staff'),
            'do' => __('Choose the trainee and enter the whole cost once. This writes an opening row of zero paid, with the full amount outstanding.'),
            'result' => __('The trainee appears on the tracking page with nothing paid and everything owing.'),
            'screen' => ['/trainee-installments/tracking', __('Payment Tracking')],
            'data' => 'trainee_installments',
            'note' => __('It can only be set once. A trainee who already has an owing cost is refused, and correcting the figure means editing the opening row.'),
        ],
        [
            'title' => __('Record each payment'),
            'who' => __('Accounting staff'),
            'do' => __('Choose the trainee and enter the amount received. The accumulated and outstanding figures are worked out for you from the previous row.'),
            'result' => __('The balance moves, and the trainee is marked settled the moment it reaches zero.'),
            'screen' => ['/trainee-installments/add', __('Record a Payment')],
        ],
        [
            'title' => __('Read who still owes'),
            'who' => __('Accounting staff'),
            'do' => __('The tracking page shows one line per trainee - paid, outstanding, and how far through they are - taken from their most recent row.'),
            'result' => __('You know who to chase and for how much.'),
            'screen' => ['/trainee-installments/tracking', __('Payment Tracking')],
        ],
        [
            'title' => __('Read who has finished'),
            'who' => __('Accounting staff'),
            'do' => __('The receipts page lists the settled rows.'),
            'result' => __('A record of who has paid in full.'),
            'screen' => ['/trainee-installments/receipts', __('Receipts')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Owing cost set once') . "] --> B[" . __('Payment recorded') . "]\n"
        . "    B --> C[" . __('Accumulated up, outstanding down') . "]\n"
        . "    C -->|" . __('outstanding above zero') . "| B\n"
        . "    C -->|" . __('outstanding zero') . "| D[" . __('Marked settled') . "]\n"
        . "    B --> E[" . __('Bookable as a journal entry') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-book',
            'what' => __('Every payment with an amount on it appears on the auto-generated journal page as cash in against training revenue, and is booked once.'),
            'url' => '/journals/auto-generated',
            'label' => __('Auto-Generated Journals'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('The figures follow the trainee. Promotion to apprentice does not carry them across, and there is no equivalent register on the apprentice side.'),
            'url' => '/trainees',
            'label' => __('Trainees'),
        ],
    ],

    'cautions' => [
        __('The running totals are computed from the previous row, so the rows have to stay in order. Deleting a payment from the middle leaves every row after it carrying figures that no longer add up - the later rows are not recalculated.'),
        __('Amounts are whole numbers. There are no cents, and a figure entered with a decimal loses it.'),
        __('A currency is stored against the row, but nothing converts anything and every screen writes the amounts as rupiah. An amount in another currency will be shown as though it were rupiah.'),
    ],
];
