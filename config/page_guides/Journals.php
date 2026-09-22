<?php
/**
 * Guide for the journal.
 */

return [
    'icon' => 'fa-book',
    'title' => __('Journals'),
    'subtitle' => __('Every amount the programme takes in or pays out, booked in double entry.'),
    'lead' => __('A journal entry has a date, a reference, a description and two or more lines, and its debits and its credits have to come to the same total. Most of them do not need typing: three things that happen elsewhere in the application - an installment paid, a ticket bought, a document cost settled - are read straight off their own screens and booked for you, each matched by its reference so the same event can never be booked twice. What is left is typed by hand on the adjustments page.'),

    'actors' => [
        ['role' => 'tmm-accounting', 'can' => __('Generates and types entries, and decides what is posted.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The accounts have to exist, and the five the automatic entries use have to keep their codes.'), 'url' => '/chart-of-accounts', 'label' => __('Chart of Accounts')],
    ],

    'steps' => [
        [
            'title' => __('Let the application book what it can'),
            'who' => __('Accounting staff'),
            'do' => __('The auto-generated page lists every operational record that has no journal yet, with the two accounts each one would touch, and books the ones you choose.'),
            'result' => __('The routine entries are on the books without anybody typing an amount twice.'),
            'screen' => ['/journals/auto-generated', __('Auto-Generated')],
            'data' => 'journals, journal_details',
            'note' => __('Matching is by reference - INST-12, TKT-3, DOC-7. A source already booked simply stops appearing.'),
        ],
        [
            'title' => __('Type what is left'),
            'who' => __('Accounting staff'),
            'do' => __('The adjustments page takes a date, a description and the lines. It will not save until the debits and the credits agree.'),
            'result' => __('An entry that balances, whatever it is for.'),
            'screen' => ['/journals/adjustments', __('Adjustments')],
        ],
        [
            'title' => __('Read and correct'),
            'who' => __('Accounting staff'),
            'do' => __('An entry can be reopened and rewritten: the lines are replaced and the totals recalculated in one transaction, so a half-rewritten entry cannot be left behind.'),
            'result' => __('The book says what happened.'),
            'screen' => ['/journals', __('Journal List')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Installment paid') . "] --> D{" . __('Not booked yet?') . "}\n"
        . "    B[" . __('Ticket bought') . "] --> D\n"
        . "    C[" . __('Document cost paid') . "] --> D\n"
        . "    D -->|" . __('yes') . "| E[" . __('Journal generated, status Posted') . "]\n"
        . "    D -->|" . __('reference already exists') . "| F[" . __('Skipped') . "]\n"
        . "    G[" . __('Anything else') . "] --> H[" . __('Typed on the adjustments page') . "]\n"
        . "    E --> I[" . __('Account balances and reports') . "]\n"
        . "    H --> I\n"
        . "    style E fill:#e3f2fd\n"
        . "    style I fill:#c8e6c9\n"
        . "    style F fill:#eeeeee",

    'triggers' => [
        [
            'icon' => 'fa-file-invoice-dollar',
            'what' => __('A payment recorded against a trainee appears on the auto-generated page as cash in against training revenue.'),
            'url' => '/trainee-installments',
            'label' => __('Trainee Installments'),
        ],
        [
            'icon' => 'fa-plane',
            'what' => __('A ticket with a price on it appears as a transport expense against accounts payable. The price is taken as it stands, and the entry is dated the day you generate it, not the day the ticket was bought.'),
            'url' => '/tickets',
            'label' => __('Tickets'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('The income statement and the balance sheet are built from these lines.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('An entry is booked from the source amount as it stood at that moment. Changing the installment, the ticket price or the document cost afterwards does not change the journal and does not bring the source back onto the pending list - the correction has to be typed.'),
        __('Amounts are booked at face value with no currency attached. A ticket priced in yen and one priced in rupiah are added together as if they were the same money.'),
        __('A source whose two accounts are not both in the chart of accounts is skipped silently. The page says only that nothing was generated, not which account is missing.'),
        __('Deleting an entry frees its reference, so the source it came from reappears on the pending list and can be booked again.'),
    ],
];
