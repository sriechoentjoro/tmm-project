<?php
/**
 * Guide for candidate medical check-up records.
 */

return [
    'icon' => 'fa-stethoscope',
    'title' => __('Candidate Medical Check-Ups'),
    'subtitle' => __('The health record behind a selection decision.'),
    'lead' => __('A medical check-up is recorded against the candidate with its result and its score. Unlike the physical test and the interview, it is kept as its own record rather than written onto the candidate: one candidate can have several, and the scoring board averages them.'),

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
            'title' => __('See it counted'),
            'who' => __('Recruitment staff'),
            'do' => __('The scoring board counts how many check-ups each candidate has and averages their final scores.'),
            'result' => __('A candidate with no check-up is visibly different from one who passed.'),
            'screen' => ['/lpk-candidate-scoring', __('Candidate scoring board')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Check-up recorded') . "] --> B[" . __('Averaged on the scoring board') . "]\n"
        . "    B --> C[" . __('Read when selecting') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style C fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-table-list',
            'what' => __('The scoring board reads these records directly - the count and the average come from here.'),
            'url' => '/lpk-candidate-scoring',
            'label' => __('Candidate scoring board'),
        ],
    ],

    'cautions' => [
        __('The promotion screen has a medical column of its own, which reads a field on the candidate that nothing writes. Judge the medical side from the scoring board or from these records, not from that column.'),
        __('Several check-ups for one candidate are averaged, not replaced. A poor early result keeps pulling the average down after a later good one.'),
    ],
];
