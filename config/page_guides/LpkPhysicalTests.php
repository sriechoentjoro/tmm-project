<?php
/**
 * Guide for the physical fitness test screen.
 */

return [
    'icon' => 'fa-dumbbell',
    'title' => __('Physical Tests'),
    'subtitle' => __('Push-ups, sit-ups and a run, turned into one score out of a hundred.'),
    'lead' => __('The institution measures three things and the system does the arithmetic. What you enter are raw counts and a running time; what comes out is a fitness score written straight onto the candidate, where recruitment reads it when deciding who goes forward.'),

    'actors' => [
        ['role' => 'lpk-penyangga', 'can' => __('Scores its own candidates only. A candidate from another institution is refused even if the address is typed by hand.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads the scores but cannot change them. The screen refuses the save, not just hides the button.')],
        ['role' => 'administrator', 'can' => __('Scores any candidate.')],
    ],

    'before' => [
        ['note' => __('The candidate has to be registered first - this screen scores people, it does not create them.'), 'url' => '/candidates', 'label' => __('Candidates')],
    ],

    'steps' => [
        [
            'title' => __('Open the candidate to be scored'),
            'who' => __('The institution'),
            'do' => __('The list shows your candidates with the score each already has, so it is easy to see who has not been tested.'),
            'result' => __('You know who is still to do.'),
            'screen' => ['/lpk-physical-tests', __('Physical Tests')],
        ],
        [
            'title' => __('Enter what was measured'),
            'who' => __('The institution'),
            'do' => __('Push-ups and sit-ups as counts, the 2.4 km run as minutes and seconds. Enter what happened; do not convert anything yourself.'),
            'result' => __('The score is computed and saved on the candidate.'),
            'data' => 'candidates.fitness_pushups, fitness_situps, fitness_running_minutes, fitness_running_seconds',
        ],
        [
            'title' => __('How the score is reached'),
            'who' => __('The system'),
            'do' => __('Push-ups count one point each up to 40. Sit-ups count one point each up to 30. The run is worth 30 points at 10 minutes or under, then 25, 20, 15 and 10 as it passes 11, 12, 13 and 14 minutes, and 5 beyond that.'),
            'result' => __('The three parts add up to a fitness score out of 100.'),
            'data' => 'candidates.fitness_score',
            'note' => __('Counts above the cap earn nothing extra. Forty-five push-ups score the same forty points as forty.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Push-ups, max 40 pts') . "] --> D[" . __('Fitness score /100') . "]\n"
        . "    B[" . __('Sit-ups, max 30 pts') . "] --> D\n"
        . "    C[" . __('2.4 km run, max 30 pts') . "] --> D\n"
        . "    D --> E[" . __('Written onto the candidate') . "]\n"
        . "    E --> F[" . __('Read on the promotion screen') . "]\n"
        . "    style D fill:#e3f2fd\n"
        . "    style F fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-plus',
            'what' => __('The score is stored on the candidate, not in a separate test record, so it appears on the candidate screens the moment it is saved.'),
            'url' => '/candidates',
            'label' => __('Candidates'),
        ],
        [
            'icon' => 'fa-th-list',
            'what' => __('The scoring board puts this figure beside the interview and medical results for the whole institution.'),
            'url' => '/lpk-candidate-scoring',
            'label' => __('Candidate scoring board'),
        ],
        [
            'icon' => 'fa-chart-line',
            'what' => __('Promoting a candidate with no physical score is possible, but the promotion records a remark saying the score was missing.'),
            'url' => '/candidates/promote-to-trainee',
            'label' => __('Promote to Trainee'),
        ],
    ],

    'cautions' => [
        __('Scoring again overwrites the previous result. There is no history of earlier attempts, so if a re-test matters, note it somewhere before saving.'),
        __('A score of zero and no test at all look the same on the list. Leave a candidate untested rather than entering zeros to fill the column.'),
    ],
];
