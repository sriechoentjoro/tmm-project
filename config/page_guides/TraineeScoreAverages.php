<?php
/**
 * Guide for the typed score averages.
 */

return [
    'icon' => 'fa-chart-line',
    'title' => __('Score Averages'),
    'subtitle' => __('A typed summary of a trainee\'s standing in one competency.'),
    'lead' => __('This screen holds an average you type: one trainee, one competency, the figure and the grade band it falls in. It is worth knowing what it is not - the averages shown on a certificate and on the promotion checklist are computed from the individual test scores every time they are read, and they do not come from here. Use this screen for a standing you want recorded deliberately, not as the place the system gets its numbers.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Records and maintains the averages.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The competency and the grade band are both master lists.'), 'url' => '/master-training-competencies', 'label' => __('Competencies')],
    ],

    'steps' => [
        [
            'title' => __('Record the average'),
            'who' => __('Training staff'),
            'do' => __('Choose the trainee and the competency, enter the average, and pick the grade band it falls in.'),
            'result' => __('The figure is on file against that trainee and competency.'),
            'screen' => ['/trainee-score-averages/add', __('New Score Average')],
            'data' => 'trainee_score_averages',
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Individual test scores') . "] --> B[" . __('Averages computed when read') . "]\n"
        . "    B --> C[" . __('Certificate and promotion checklist') . "]\n"
        . "    D[" . __('Average typed here') . "] --> E[" . __('Kept as a record') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style C fill:#c8e6c9\n"
        . "    style E fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-edit',
            'what' => __('The individual test scores are the source the computed averages come from. If a certificate looks wrong, that is where to look, not here.'),
            'url' => '/trainee-training-test-scores',
            'label' => __('Training Test Scores'),
        ],
    ],

    'cautions' => [
        __('The average and the grade are typed into the form, not calculated. They were right when somebody entered them and drift as soon as a test score is entered, corrected or deleted. The page now works both out from the scores and shows the real figure beside the stored one with an arrow, and says how many rows have drifted.'),
        __('The grade is not a judgement to make here: the score bands decide it, so the band an average falls in is the grade. Where an average falls in no band at all, no grade is claimed and the row is not blamed - that is a gap in the bands, not a mistake in the row.'),
        __('These figures are read by nothing else. The departure screen works out its own average straight from the test scores, pooling every competency, so the two can disagree without either being consulted about it.'),
        __('Nothing else in the application currently reads these figures: no dashboard, no report, and not the certificate. Typing an average here will not change any of them.'),
        __('Because it is typed rather than derived, an average here can drift out of step with the test scores it was meant to summarise. Re-check it when new tests are recorded.'),
    ],
];
