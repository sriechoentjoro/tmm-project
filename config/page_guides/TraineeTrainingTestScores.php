<?php
/**
 * Guide for trainee test scores.
 */

return [
    'icon' => 'fa-pen-to-square',
    'title' => __('Training Test Scores'),
    'subtitle' => __('One trainee, one competency, one score, on one date.'),
    'lead' => __('Every test a trainee sits is recorded as its own row: which competency, what score, what grade, and when. Nothing here is a total - the averages that appear on certificates and on the promotion checklist are computed from these rows each time they are read, so a correction here corrects everything downstream at once.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Records and corrects scores.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The competency being tested and the grade bands both come from master lists, so a test cannot be recorded against a subject nobody has defined.'), 'url' => '/master-training-competencies', 'label' => __('Competencies')],
    ],

    'steps' => [
        [
            'title' => __('Enter a room\'s scores together'),
            'who' => __('Training staff'),
            'do' => __('The daily screen asks for a date and then shows that day\'s tests in one place, which is faster and less error-prone than opening one trainee at a time.'),
            'result' => __('The day\'s results are on file.'),
            'screen' => ['/trainee-training-test-scores/daily', __('Daily test scores')],
            'data' => 'trainee_training_test_scores.test_date',
        ],
        [
            'title' => __('Record a single test'),
            'who' => __('Training staff'),
            'do' => __('Choose the trainee, the competency, the score and the grade band it falls in.'),
            'result' => __('One row, which every average downstream will include.'),
            'screen' => ['/trainee-training-test-scores/add', __('New Test Score')],
        ],
        [
            'title' => __('Read the summary'),
            'who' => __('Training staff'),
            'do' => __('The report gathers the scores into something you can look at rather than scroll through.'),
            'result' => __('Where a trainee or a competency is weak becomes visible.'),
            'screen' => ['/trainee-training-test-scores/report', __('Score report')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Test recorded') . "] --> B[" . __('Average on the promotion checklist') . "]\n"
        . "    A --> C[" . __('Averages on the certificate') . "]\n"
        . "    A --> D[" . __('Score report') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style C fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-certificate',
            'what' => __('The certificate page computes the average, the highest and the lowest score, and how many tests were passed, straight from these rows. It does not read any stored average.'),
            'url' => '/trainee-certificates',
            'label' => __('Certificates'),
        ],
        [
            'icon' => 'fa-clipboard-check',
            'what' => __('The promotion checklist shows each trainee\'s average from here beside their name.'),
            'url' => '/trainees/promotion-checklist',
            'label' => __('Promotion checklist'),
        ],
    ],

    'cautions' => [
        __('Because the averages are computed rather than stored, deleting a test changes the certificate of a trainee who may already hold the printed copy. Correct a score rather than deleting and re-entering it where you can.'),
    ],
];
