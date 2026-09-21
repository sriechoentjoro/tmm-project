<?php
/**
 * Guide for trainee certificates.
 */

return [
    'icon' => 'fa-certificate',
    'title' => __('Trainee Certificates'),
    'subtitle' => __('The certificate a trainee receives at the end of a batch, and the scores behind it.'),
    'lead' => __('A certificate ties a trainee to the batch they finished and the number on the paper they were handed. What makes it useful is what it gathers: opening one computes the trainee\'s average, highest and lowest test scores and how many tests they passed, straight from the individual test records. Nothing is stored twice, so a score corrected later shows corrected here.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Issues certificates and prints them.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The test scores have to be recorded first: the certificate reads them rather than asking for them.'), 'url' => '/trainee-training-test-scores', 'label' => __('Training Test Scores')],
    ],

    'steps' => [
        [
            'title' => __('Issue the certificate'),
            'who' => __('Training staff'),
            'do' => __('Choose the trainee and the batch, and record the certificate number exactly as it appears on the paper.'),
            'result' => __('The certificate is on file and can be opened or printed.'),
            'screen' => ['/trainee-certificates/add', __('Issue a Certificate')],
            'data' => 'trainee_certificates',
        ],
        [
            'title' => __('Read what it gathered'),
            'who' => __('Training staff'),
            'do' => __('The detail page shows the per-competency breakdown with each grade, and the summary figures above it.'),
            'result' => __('The certificate is backed by the record, not by memory.'),
        ],
        [
            'title' => __('Print it'),
            'who' => __('Training staff'),
            'do' => __('The print view lays the same information out for paper.'),
            'result' => __('A printed certificate whose figures match what is on file.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Test scores recorded') . "] --> B[" . __('Certificate issued') . "]\n"
        . "    B --> C[" . __('Averages computed on open') . "]\n"
        . "    C --> D[" . __('Printed certificate') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-pen-to-square',
            'what' => __('Every figure on the certificate is computed from the test scores when the page is opened. Correcting a score corrects the certificate; deleting one changes a certificate that may already be printed.'),
            'url' => '/trainee-training-test-scores',
            'label' => __('Training Test Scores'),
        ],
        [
            'icon' => 'fa-layer-group',
            'what' => __('The batch named on the certificate comes from the batch register.'),
            'url' => '/trainee-training-batches',
            'label' => __('Training Batches'),
        ],
    ],

    'cautions' => [
        __('The certificate number is yours to keep consistent - nothing generates it, and nothing checks that two certificates do not share one.'),
        __('Notes recorded against a certificate are kept on file only; they do not appear on the printed certificate.'),
    ],
];
