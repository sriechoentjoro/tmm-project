<?php
/**
 * Guide for candidate medical check-up records.
 */

return [
    'icon' => 'fa-stethoscope',
    'title' => __('Candidate Medical Check-Ups'),
    'subtitle' => __('The health record behind a selection decision.'),
    'lead' => __('A medical check-up is recorded against the candidate with its result. The result type carries the meaning - an administrator says once whether each one counts as medically fit - and from that the candidate\'s standing is worked out the moment the check-up is saved. A candidate marked not fit cannot be put forward for promotion, and the screen that entered the result is the one that says so.'),

    'actors' => [
        ['role' => 'lpk-penyangga', 'can' => __('Records check-ups for its own candidates.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads them when deciding who goes forward.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The candidate has to exist before a check-up can be recorded against them.'), 'url' => '/candidates', 'label' => __('Candidates')],
    ],

    'steps' => [
        [
            'title' => __('Record the check-up'),
            'who' => __('The institution'),
            'do' => __('Enter the date, the result and the final score from the examining clinic.'),
            'result' => __('The check-up is on file against that candidate.'),
            'screen' => ['/candidate-record-medical-check-ups/add', __('New Medical Check-Up')],
            'data' => 'candidate_record_medical_check_ups.final_score',
        ],
        [
            'title' => __('The standing is decided for you'),
            'who' => __('The system'),
            'do' => __('The moment the check-up is saved, the candidate\'s medical standing is recomputed from all of their check-ups. It is cautious: one result marked not fit makes the candidate not fit, whatever the others say. A result type nobody has marked counts for nothing either way.'),
            'result' => __('The candidate carries pass or fail, and a failing one is held back from the promotion list.'),
            'data' => 'candidates.mcu_result',
            'note' => __('If the standing stays blank, nobody has said what that result type means. It is set once per type on the MCU result master screen.'),
        ],
        [
            'title' => __('See it counted'),
            'who' => __('Recruitment staff'),
            'do' => __('The scoring board counts how many check-ups each candidate has and averages their final scores.'),
            'result' => __('A candidate with no check-up is visibly different from one who passed.'),
            'screen' => ['/lpk-candidate-scoring', __('Candidate scoring board')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Check-up recorded') . "] --> B{" . __('Result type marked fit?') . "}\n"
        . "    B -->|" . __('not fit') . "| C[" . __('Candidate held back') . "]\n"
        . "    B -->|" . __('fit') . "| D[" . __('Candidate may go forward') . "]\n"
        . "    B -->|" . __('nobody said') . "| E[" . __('Counts for nothing either way') . "]\n"
        . "    D --> F[" . __('Averaged on the scoring board') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style C fill:#ffebee\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-sliders-h',
            'what' => __('What each result type means - fit, not fit, or not said - is set once on the master screen, and every check-up recorded with that result inherits it.'),
            'url' => '/master-medical-check-up-results',
            'label' => __('MCU result types'),
        ],
        [
            'icon' => 'fa-ban',
            'what' => __('A candidate marked not fit cannot be put forward by their institution and cannot be promoted by recruitment. Both screens refuse it and say why.'),
            'url' => '/candidates/promote-to-trainee',
            'label' => __('Promote to Trainee'),
        ],
        [
            'icon' => 'fa-th-list',
            'what' => __('The scoring board reads these records directly - the count and the average come from here.'),
            'url' => '/lpk-candidate-scoring',
            'label' => __('Candidate scoring board'),
        ],
    ],

    'cautions' => [
        __('Changing what a result means, on the master screen, re-checks everybody who ever received it. A result switched to not fit can therefore stop people who were passing this morning, and the screen says how many.'),
        __('Deleting a check-up recomputes the standing too, so removing the only failing one lets the candidate go forward again. That is intended, but it means a deletion is a decision, not just tidying up.'),
        __('Several check-ups for one candidate are averaged, not replaced. A poor early result keeps pulling the average down after a later good one.'),
    ],
];
