<?php
/**
 * Guide for the apprentice document register - the second view over the
 * apprentice_submission_documents table.
 */

return [
    'icon' => 'fa-copy',
    'title' => __('Apprentice Document Register'),
    'subtitle' => __('The same documents as the submission page, listed with filters and a status summary.'),
    'lead' => __('This page and the Submission Documents page read and write the same table. They are not two registers that need reconciling: they are two doors into one. What this one adds is the working view - filter by apprentice, by document or by status, and a count per status across everything, so the question "how much is left" is answered at the top of the page rather than by scrolling.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Adds, edits and filters document rows.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The required documents and the statuses are master lists, shared with the submission page.'), 'url' => '/master-apprentice-submission-documents', 'label' => __('Required Documents')],
    ],

    'steps' => [
        [
            'title' => __('Narrow to what you are working on'),
            'who' => __('Apprentice staff'),
            'do' => __('Filter by apprentice when you are preparing one person, or by document when you are collecting one paper from everybody.'),
            'result' => __('The list is the batch of work in front of you.'),
            'screen' => ['/apprentice-documents', __('Document Register')],
            'data' => 'apprentice_submission_documents',
        ],
        [
            'title' => __('Read the status summary'),
            'who' => __('Apprentice staff'),
            'do' => __('The counts at the top are over every row, not over the filtered list.'),
            'result' => __('Progress across the whole group is visible even while you work on one person.'),
            'note' => __('The summary ignores the filters on purpose. A filtered list with an unchanged total is not a bug.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Submission Documents page') . "] --> C[(" . __('One table') . ")]\n"
        . "    B[" . __('Document Register page') . "] --> C\n"
        . "    C --> D[" . __('Status summary') . "]\n"
        . "    style C fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-folder-open',
            'what' => __('The Submission Documents page writes these same rows. Anything done there shows here immediately, and the other way round.'),
            'url' => '/apprentice-submission-documents',
            'label' => __('Submission Documents'),
        ],
        [
            'icon' => 'fa-user-tie',
            'what' => __('Every row names an apprentice. Deleting the apprentice does not delete their documents.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
    ],

    'cautions' => [
        __('Because both pages write one table, a row deleted here is gone from the submission page as well. There is no second copy to fall back on.'),
        __('The two pages show different columns for the same row. A field you cannot see on one of them is not empty - it is just not on that screen.'),
    ],
];
