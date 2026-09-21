<?php
/**
 * Guide for candidate documents and the submission checklist.
 */

return [
    'icon' => 'fa-folder-open',
    'title' => __('Candidate Documents'),
    'subtitle' => __('What each candidate has handed in, and what is still missing.'),
    'lead' => __('Selection needs paperwork as much as scores. This is where a candidate document is uploaded and where the checklist shows, per candidate, which of the required documents are in. Document completeness is one of the three things the promotion screen puts in front of recruitment.'),

    'actors' => [
        ['role' => 'lpk-penyangga', 'can' => __('Uploads and maintains documents for its own candidates.')],
        ['role' => 'tmm-recruitment', 'can' => __('Checks completeness before promoting.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('Which documents are required is a master list. A document nobody added there cannot be checked off here.'), 'url' => '/candidate-documents-master-list', 'label' => __('Required documents')],
    ],

    'steps' => [
        [
            'title' => __('Upload what the candidate hands in'),
            'who' => __('The institution'),
            'do' => __('Attach the file against the candidate and the document it satisfies.'),
            'result' => __('The document is on file and counts towards completeness.'),
            'screen' => ['/candidate-documents/add', __('Upload a document')],
            'data' => 'candidate_documents',
        ],
        [
            'title' => __('Read the checklist'),
            'who' => __('The institution or recruitment'),
            'do' => __('The checklist shows the required documents against what has arrived, one row per candidate.'),
            'result' => __('What is missing is visible without opening each candidate.'),
            'screen' => ['/candidate-documents/submission-checklist', __('Document checklist')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Required document list') . "] --> B[" . __('Checklist per candidate') . "]\n"
        . "    C[" . __('Uploaded document') . "] --> B\n"
        . "    B --> D[" . __('Completeness on the promotion screen') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-arrow-up-right-dots',
            'what' => __('The promotion screen shows document completeness beside the scores, so what is uploaded here changes what recruitment sees there.'),
            'url' => '/candidates/promote-to-trainee',
            'label' => __('Promote to Trainee'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Documents do not follow a candidate into the trainee phase - trainee documents are collected separately.'),
            'url' => '/trainee-submission-documents',
            'label' => __('Trainee Documents'),
        ],
    ],

    'cautions' => [
        __('Completeness counts documents, not their quality. A blurred photograph of the right document counts as present.'),
        __('Re-uploading a document a candidate already submitted replaces the existing record rather than adding a second one.'),
    ],
];
