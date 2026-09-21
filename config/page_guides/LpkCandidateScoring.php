<?php
/**
 * Guide for the candidate scoring board.
 */

return [
    'icon' => 'fa-table-list',
    'title' => __('Candidate Scoring Board'),
    'subtitle' => __('The three selection results, side by side, for a whole institution.'),
    'lead' => __('This screen is a scoreboard, not a form. It gathers the physical test score, the interviews and the medical check-ups for every candidate of an institution and puts them in one table, so selection is a reading exercise rather than a hunt across three screens. Nothing here can be changed; each figure belongs to the screen that produced it.'),

    'actors' => [
        ['role' => 'lpk-penyangga', 'can' => __('Sees its own candidates only.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads every institution, which is the point: it is the comparison view before promotion.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'steps' => [
        [
            'title' => __('Read the three columns together'),
            'who' => __('Recruitment staff or the institution'),
            'do' => __('Fitness comes from the physical test, the interview count and result from the interview records, the medical count and average from the check-ups.'),
            'result' => __('A candidate who is ready shows three filled columns; one who is not shows where the gap is.'),
            'screen' => ['/lpk-candidate-scoring', __('Candidate scoring board')],
        ],
        [
            'title' => __('Fill the gaps at their source'),
            'who' => __('The institution'),
            'do' => __('An empty column is filled on the screen that owns it, not here.'),
            'result' => __('The board fills in as the work is done.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Physical test') . "] --> D[" . __('Scoring board') . "]\n"
        . "    B[" . __('Interviews') . "] --> D\n"
        . "    C[" . __('Medical check-ups') . "] --> D\n"
        . "    D --> E[" . __('Selection decision') . "]\n"
        . "    style D fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        ['icon' => 'fa-dumbbell', 'what' => __('The fitness column is written by the physical test screen.'), 'url' => '/lpk-physical-tests', 'label' => __('Physical Tests')],
        ['icon' => 'fa-comments', 'what' => __('The interview columns come from the interview records.'), 'url' => '/candidate-record-interviews', 'label' => __('Interviews')],
        ['icon' => 'fa-stethoscope', 'what' => __('The medical columns are counted and averaged from the check-up records.'), 'url' => '/candidate-record-medical-check-ups', 'label' => __('Medical Check-Ups')],
    ],

    'cautions' => [
        __('An average of one check-up is that one check-up. Read the count beside it before trusting the number.'),
    ],
];
