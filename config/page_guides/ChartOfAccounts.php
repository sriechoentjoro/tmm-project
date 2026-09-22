<?php
/**
 * Guide for the chart of accounts.
 */

return [
    'icon' => 'fa-sitemap',
    'title' => __('Chart of Accounts'),
    'subtitle' => __('The list of accounts every journal line has to name.'),
    'lead' => __('Nothing can be booked against an account that does not exist here, so this list is the vocabulary the whole accounting side speaks. Each account has a code, a name and a type - asset, liability, equity, revenue or expense - and the type is what decides which way its balance runs. The hierarchy page groups the accounts by type and shows what each one has accumulated, worked out from the journal lines rather than stored.'),

    'actors' => [
        ['role' => 'tmm-accounting', 'can' => __('Maintains the accounts and reads the balances.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('Nothing has to exist first. This is the foundation the journals stand on, so it is set up before anything is booked.')],
    ],

    'steps' => [
        [
            'title' => __('Add the account'),
            'who' => __('Accounting staff'),
            'do' => __('Give it a code, a name, and the type it belongs to.'),
            'result' => __('The account can be named on a journal line.'),
            'screen' => ['/chart-of-accounts/add', __('Add Account')],
            'data' => 'chart_of_accounts',
            'note' => __('The code is what the automatic journals look for, not the name. Renaming an account is safe; changing its code is not.'),
        ],
        [
            'title' => __('Read what has accumulated'),
            'who' => __('Accounting staff'),
            'do' => __('The hierarchy page groups the accounts by type, totals each group, and shows how many journal lines are behind every figure.'),
            'result' => __('The state of the books without opening a single journal.'),
            'screen' => ['/chart-of-accounts/hierarchy', __('Hierarchy')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Account created here') . "] --> B[" . __('Named on a journal line') . "]\n"
        . "    B --> C[" . __('Balance accumulates') . "]\n"
        . "    C --> D[" . __('Shown on the hierarchy page') . "]\n"
        . "    C --> E[" . __('Shown in the financial reports') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-book',
            'what' => __('The automatic journals look up five accounts by their code: 1000 cash, 2000 accounts payable, 4000 training revenue, 5100 document expense, 5200 transport expense. A source whose two accounts are not both present is skipped without a word, so those five codes have to exist and keep their codes.'),
            'url' => '/journals/auto-generated',
            'label' => __('Auto-Generated Journals'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('The income statement and the balance sheet are this list with its accumulated figures, split by type.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('The balances are not stored anywhere. They are added up from the journal lines each time a page is opened, so they cannot drift - but they also cannot be corrected here. A wrong balance is a wrong journal.'),
        __('Deleting an account that journal lines already name leaves those lines pointing at nothing, and the amount disappears from every total. Nothing stops the deletion.'),
        __('An account with no type behaves as its own group and is left out of the reports, which ask for named types.'),
    ],
];
