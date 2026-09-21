<?php
/**
 * Guide for apprentice submission documents.
 */

return [
    'icon' => 'fa-folder-open',
    'title' => __('Apprentice Submission Documents'),
    'subtitle' => __('The documents an apprentice hands in before departure, and the state each one is in.'),
    'lead' => __('Departure is a paperwork queue. Each required document is a master entry, and each apprentice needs a row against it: the file itself, a status, who uploaded it and when, and a note if the status alone does not explain matters. The point of the register is not storage - it is being able to answer, for one apprentice or for the whole group, what is still outstanding and who has it.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Uploads documents, sets their status and chases what is missing.')],
        ['role' => 'administrator', 'can' => __('Everything, including the master list of required documents.')],
    ],

    'before' => [
        ['note' => __('Which documents are required is a master list. A document not on that list cannot be recorded against anyone.'), 'url' => '/master-apprentice-submission-documents', 'label' => __('Required Documents')],
        ['note' => __('The statuses a document can be in are a master list too, so the summary counts can be trusted.'), 'url' => '/master-document-submission-statuses', 'label' => __('Submission Statuses')],
    ],

    'steps' => [
        [
            'title' => __('Upload what arrives'),
            'who' => __('Apprentice staff'),
            'do' => __('Attach the file against the apprentice and the document it satisfies, and set the status.'),
            'result' => __('The document is on file with its state, not just in a folder somewhere.'),
            'screen' => ['/apprentice-submission-documents/add', __('Add Document')],
            'data' => 'apprentice_submission_documents',
        ],
        [
            'title' => __('Move the status as it changes'),
            'who' => __('Apprentice staff'),
            'do' => __('A document that comes back for correction, or that is finally accepted, gets its status changed rather than a second row.'),
            'result' => __('One row per document per apprentice, holding the current truth.'),
            'note' => __('Re-uploading against the same document replaces the file on the existing row rather than adding a second one.'),
        ],
        [
            'title' => __('Read what is outstanding'),
            'who' => __('Apprentice staff'),
            'do' => __('The list totals the documents by status, so the size of the queue is visible without opening anyone.'),
            'result' => __('You know who to chase and for what.'),
            'screen' => ['/apprentice-documents', __('Document Register')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Required document list') . "] --> B[" . __('Row per apprentice') . "]\n"
        . "    B --> C[" . __('File uploaded') . "]\n"
        . "    C --> D[" . __('Status set') . "]\n"
        . "    D --> E[" . __('Counted in the summary') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-copy',
            'what' => __('The Document Register page is a second view over these same rows - the same table, reached by a different menu entry. A row added on either page appears on both, and deleting it on one deletes it on the other.'),
            'url' => '/apprentice-documents',
            'label' => __('Document Register'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('How many required documents are accepted here is one of the three things TMM Training weighs before recording a departure. It is counted live from these rows, so accepting a document changes what training sees at once.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('The document dashboard is meant to summarise readiness, but its totals are typed in by hand rather than counted from these rows.'),
            'url' => '/apprentice-document-management-dashboards',
            'label' => __('Document Dashboard'),
        ],
    ],

    'cautions' => [
        __('A required document with no row at all is invisible here: the list shows what was handed in, not what is missing. Missing means "no row", and only the required-document master list says what should have been there.'),
        __('Nothing prevents two rows for the same apprentice and the same document. Where that happens the summary counts both.'),
    ],
];
