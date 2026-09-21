<?php
/**
 * Guide for the apprentice document management dashboard.
 */

return [
    'icon' => 'fa-chart-pie',
    'title' => __('Document Dashboard'),
    'subtitle' => __('A stored count of how many documents are ready, pending and missing.'),
    'lead' => __('This page holds one row per person with four numbers on it: how many documents there are in total, how many are ready, how many are pending and how many are missing, plus the date the row was last brought up to date. It is worth knowing before you use it that these numbers are typed in, not counted. Nothing in the application recalculates them when a document is uploaded or a status changes, so the row says what it said the last time somebody edited it.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Maintains the rows by hand.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The documents themselves live in the document register, and that is where the real state is.'), 'url' => '/apprentice-documents', 'label' => __('Document Register')],
    ],

    'steps' => [
        [
            'title' => __('Count the documents'),
            'who' => __('Apprentice staff'),
            'do' => __('Read the real figures off the document register, which counts by status across every row.'),
            'result' => __('You have the numbers this page is supposed to hold.'),
            'screen' => ['/apprentice-documents', __('Document Register')],
        ],
        [
            'title' => __('Write them down here'),
            'who' => __('Apprentice staff'),
            'do' => __('Enter the four totals against the person and set the last-updated date, so a reader knows how old the figures are.'),
            'result' => __('A summary that is honest about when it was true.'),
            'screen' => ['/apprentice-document-management-dashboards/add', __('Add Dashboard Row')],
            'data' => 'apprentice_document_management_dashboards',
            'note' => __('If you cannot keep the date current, the numbers are worse than no numbers.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Documents uploaded and statuses set') . "] --> B[" . __('Counted by the register') . "]\n"
        . "    B -. " . __('by hand') . " .-> C[" . __('Totals typed here') . "]\n"
        . "    C --> D[" . __('Read by whoever opens this page') . "]\n"
        . "    style C fill:#fff3e0\n"
        . "    style B fill:#e3f2fd",

    'triggers' => [
        [
            'icon' => 'fa-folder-open',
            'what' => __('Uploading a document or changing its status does not touch these totals. Nothing writes to this table except this form.'),
            'url' => '/apprentice-submission-documents',
            'label' => __('Submission Documents'),
        ],
    ],

    'cautions' => [
        __('The row is keyed to a candidate, not to an apprentice, and it lives in the candidate document database rather than the apprentice one. On a page named for apprentices that is easy to read past, so check you are choosing the person you mean.'),
        __('Nothing else in the application reads these figures. No report, no other dashboard, no export. They are here for a person to look at, and nowhere else.'),
        __('Because they are typed, the totals can contradict the register. Where they do, the register is right.'),
    ],
];
