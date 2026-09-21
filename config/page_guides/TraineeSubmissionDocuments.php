<?php
/**
 * Guide for trainee documents, their checklist and their costs.
 */

return [
    'icon' => 'fa-folder-open',
    'title' => __('Trainee Documents'),
    'subtitle' => __('What each trainee has submitted, what is missing, and what it cost.'),
    'lead' => __('Departure runs on paperwork. This is where a trainee\'s documents are uploaded, where the checklist shows which required ones are still missing, and where the money each document cost is recorded against whether it has been paid. Candidate documents do not follow a person into the training phase, so this collection starts fresh.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Uploads documents, chases the missing ones, records the costs.')],
        ['role' => 'tmm-training', 'can' => __('Reads the checklist to know whether a trainee is ready.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('Which documents are required is a master list, and only those marked required are counted by the checklist.'), 'url' => '/master-apprentice-submission-documents', 'label' => __('Required documents')],
    ],

    'steps' => [
        [
            'title' => __('Upload what arrives'),
            'who' => __('Documentation staff'),
            'do' => __('Attach the file against the trainee and the document it satisfies, and set its status.'),
            'result' => __('The document counts towards that trainee\'s completeness.'),
            'screen' => ['/trainee-submission-documents/add', __('Upload a document')],
            'data' => 'trainee_submission_documents',
            'note' => __('Re-uploading a document a trainee already submitted replaces the existing record rather than adding a second one.'),
        ],
        [
            'title' => __('Work from the checklist'),
            'who' => __('Documentation staff'),
            'do' => __('The checklist shows the required documents against what has arrived, one row per trainee, so the gaps are visible without opening anybody.'),
            'result' => __('You know who to chase and for what.'),
            'screen' => ['/trainee-submission-documents/checklist', __('Document checklist')],
        ],
        [
            'title' => __('Record what it cost'),
            'who' => __('Documentation staff'),
            'do' => __('Each document cost is recorded with its amount, its currency and whether it has been paid.'),
            'result' => __('The outstanding total is visible beside the paid one.'),
            'screen' => ['/trainee-submission-documents/costs', __('Document costs')],
            'data' => 'trainee_document_costs',
        ],
        [
            'title' => __('Watch the progress'),
            'who' => __('Documentation or training staff'),
            'do' => __('The progress screen counts the documents by status across everybody.'),
            'result' => __('How far the whole cohort has got, in one view.'),
            'screen' => ['/trainee-submission-documents/progress', __('Progress')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Required document list') . "] --> B[" . __('Checklist per trainee') . "]\n"
        . "    C[" . __('Uploaded document') . "] --> B\n"
        . "    C --> D[" . __('Cost recorded') . "]\n"
        . "    B --> E[" . __('Progress across the cohort') . "]\n"
        . "    D --> F[" . __('Paid and outstanding') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Readiness to be promoted to apprentice is partly a paperwork question, and this is where that part is answered.'),
            'url' => '/trainees/promote-to-apprentice',
            'label' => __('Promote to Apprentice'),
        ],
        [
            'icon' => 'fa-file-invoice-dollar',
            'what' => __('Document costs are their own record here. They are not the same thing as the installments a trainee pays, which live in the accounting screens.'),
            'url' => '/trainee-installments',
            'label' => __('Trainee Installments'),
        ],
    ],

    'cautions' => [
        __('Completeness counts documents, not their quality. A blurred photograph of the right document counts as present.'),
        __('A document marked as costing something but never marked paid stays in the outstanding total indefinitely. The total is only as honest as the marking.'),
    ],
];
