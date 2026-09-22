<?php
/**
 * Guide for the reports.
 */

return [
    'icon' => 'fa-chart-pie',
    'title' => __('Reports'),
    'subtitle' => __('The pipeline counted, and the books summarised.'),
    'lead' => __('Six reports over two different kinds of thing. Three of them count people - how many candidates, trainees and apprentices there are and how far each has got - and read the operational records directly. The other three summarise the books, and read nothing but the journal lines. Both kinds are worked out when you open them, so nothing here is ever stale; what they are is only ever as good as what was recorded underneath.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Reads everything.')],
        ['role' => 'tmm-accounting', 'can' => __('Reads the financial three.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads the pipeline.')],
    ],

    'before' => [
        ['note' => __('The financial reports have nothing to show until entries exist in the journal.'), 'url' => '/journals', 'label' => __('Journals')],
        ['note' => __('The account types decide which report an account appears in, so an account with no type appears in neither.'), 'url' => '/chart-of-accounts', 'label' => __('Chart of Accounts')],
    ],

    'steps' => [
        [
            'title' => __('Count the pipeline'),
            'who' => __('Anyone with access'),
            'do' => __('The candidate pipeline, the training progress and the active apprentices each read their own side of the application and count it as it stands.'),
            'result' => __('How many people are at each stage, without anybody keeping a tally.'),
            'screen' => ['/reports/candidate-pipeline', __('Candidate Pipeline')],
        ],
        [
            'title' => __('Read the income statement'),
            'who' => __('Accounting staff'),
            'do' => __('Every revenue and expense account, with what it has accumulated.'),
            'result' => __('What came in and what went out.'),
            'screen' => ['/reports/income-statement', __('Income Statement')],
            'note' => __('It covers everything ever booked. There is no date range, so this is not a statement for a month or a year.'),
        ],
        [
            'title' => __('Read the balance sheet'),
            'who' => __('Accounting staff'),
            'do' => __('Every asset, liability and equity account, with what it has accumulated.'),
            'result' => __('What is owned and what is owed.'),
            'screen' => ['/reports/balance-sheet', __('Balance Sheet')],
        ],
        [
            'title' => __('Read the monthly summary'),
            'who' => __('Accounting staff'),
            'do' => __('Journals grouped by the month they are dated, with how many entries and what they totalled.'),
            'result' => __('The shape of the year at a glance.'),
            'screen' => ['/reports/cash-flow', __('Cash Flow')],
            'note' => __('This counts every entry in the month, not just the ones touching cash. It shows how busy a month was, not how much cash moved.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Candidates, trainees, apprentices') . "] --> B[" . __('Pipeline reports') . "]\n"
        . "    C[" . __('Journal lines') . "] --> D[" . __('Income statement') . "]\n"
        . "    C --> E[" . __('Balance sheet') . "]\n"
        . "    C --> F[" . __('Monthly summary') . "]\n"
        . "    G[" . __('Chart of accounts: the type') . "] --> D\n"
        . "    G --> E\n"
        . "    style B fill:#c8e6c9\n"
        . "    style D fill:#e3f2fd\n"
        . "    style E fill:#e3f2fd",

    'triggers' => [
        [
            'icon' => 'fa-book',
            'what' => __('Every figure in the three financial reports comes from the journal lines. Booking an entry changes them at once; there is nothing to refresh.'),
            'url' => '/journals',
            'label' => __('Journals'),
        ],
        [
            'icon' => 'fa-sitemap',
            'what' => __('Which report an account lands in is decided by its type, and the sign its balance is shown with is decided by that type too.'),
            'url' => '/chart-of-accounts/hierarchy',
            'label' => __('Account Hierarchy'),
        ],
        [
            'icon' => 'fa-user-tie',
            'what' => __('The apprentice report counts two flags on the apprentice record: in Japan, and completed. Both are set by TMM Training and by nothing else, so a report showing zero usually means nobody has made the call rather than that nobody has gone.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
    ],

    'cautions' => [
        __('The financial reports count every journal line whatever the entry\'s status. An entry marked Void is still counted, and so is one still in Draft - marking an entry Void removes it from nothing.'),
        __('The income statement has no period. It is everything since the books began, so it cannot be compared with last month or last year.'),
        __('The page called Cash Flow is a monthly summary of journal totals, not a cash flow statement. Read it as how much was booked, not how much cash moved.'),
    ],
];
