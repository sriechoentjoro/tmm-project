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
            'note' => __('If the same trainee already has a payment of that amount on that date, the screen stops and names it rather than saving. Two genuine payments on one day are possible, so you can record it anyway - but a second copy made by accident moves the balance by a whole payment and nothing later notices.'),
        ],
        [
            'title' => __('Correct a payment'),
            'who' => __('Accounting staff'),
            'do' => __('Edit the row, or delete it if it should never have been recorded. The chain is walked again from the opening row, so every later payment gets the accumulated and outstanding figures it should have had.'),
            'result' => __('The tracking page shows what the remaining payments really come to, and a message names how many rows were recalculated.'),
            'screen' => ['/trainee-installments', __('All Payments')],
            'note' => __('Only the running totals are rewritten. The amounts and dates you entered are left exactly as they are.'),
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
        __('The running totals are derived, not typed. Correcting or removing a payment in the middle recalculates every row after it, and the page tells you how many were recalculated - so the figures always add up to the payments actually on file.'),
        __('Chains recorded before that recalculation existed can still be wrong. An administrator can list them with the repair command, which reports what each trainee is shown as owing against what their payments really come to, and rewrites only the running totals - never the payments.'),
        __('Amounts are whole numbers. There are no cents, and a figure entered with a decimal loses it.'),
        __('Each row stores its own currency and the table shows it, but the Collected and Outstanding figures at the top are plain sums written as rupiah. Where any trainee is recorded in another currency the page says so above those figures, with how many - nothing converts between currencies anywhere in this system.'),
        __('An owing cost set here is stamped with the rupiah entry from the currency master list, found by its code rather than by a fixed number, so it stays right if that list is ever renumbered.'),
    ],
];
