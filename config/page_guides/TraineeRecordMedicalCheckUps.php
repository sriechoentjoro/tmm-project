<?php
/**
 * Guide for trainee medical check-ups.
 */

return [
    'icon' => 'fa-stethoscope',
    'title' => __('Trainee Medical Check-Ups'),
    'subtitle' => __('The health check a trainee takes, and the result on file.'),
    'lead' => __('The check-up taken during selection is months old by the time training ends, and the receiving side asks for a current one. Each record names the clinic, the date, the result chosen from the shared master list, a comment and the scanned file. Several records for one trainee are normal: a re-check after treatment is a second examination, not a correction of the first.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Arranges the examination and records the result.')],
        ['role' => 'administrator', 'can' => __('Everything, including what each result means.')],
    ],

    'before' => [
        ['note' => __('The result values come from one master list, shared with the candidate and apprentice check-ups.'), 'url' => '/master-medical-check-up-results', 'label' => __('MCU Results')],
    ],

    'steps' => [
        [
            'title' => __('Record the examination'),
            'who' => __('Documentation staff'),
            'do' => __('Enter the trainee, the clinic, the date, and a title that says which examination this was.'),
            'result' => __('The examination is on file with the clinic that performed it.'),
            'screen' => ['/trainee-record-medical-check-ups/add', __('Add Medical Check-Up')],
            'data' => 'trainee_record_medical_check_ups',
        ],
        [
            'title' => __('Choose the result and attach the scan'),
            'who' => __('Documentation staff'),
            'do' => __('Pick the result from the master list and attach the file. Anything the result does not cover goes in the comment.'),
            'result' => __('The result is a value that can be counted, not a sentence somebody has to read.'),
            'note' => __('The same master list decides pass or fail automatically for candidates and refuses a departure for apprentices. On the trainee side nothing is derived from it - the result is recorded and read, and that is all.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Examination at the clinic') . "] --> B[" . __('Result recorded here') . "]\n"
        . "    B --> C[" . __('Scan attached') . "]\n"
        . "    C --> D[" . __('Readiness grid ticks its box') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-sliders-h',
            'what' => __('What each result means - fit or not fit - is maintained in one master list used by all three sides. Changing it re-checks the candidates and apprentices who hold that result; the trainees are not re-checked, because nothing on this side derives anything from it.'),
            'url' => '/master-medical-check-up-results',
            'label' => __('MCU Results'),
        ],
        [
            'icon' => 'fa-clipboard-check',
            'what' => __('The readiness grid ticks the medical column as soon as a row exists here. It does not look at whether the result was fit.'),
            'url' => '/trainee-documents/departure',
            'label' => __('Departure Readiness'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Promotion to apprentice does not copy this record. An apprentice is examined again, and that examination is what can refuse a departure.'),
            'url' => '/apprentice-record-medical-check-ups',
            'label' => __('Apprentice Medical Check-Ups'),
        ],
    ],

    'cautions' => [
        __('A result marked not fit stops nothing here. On this side it is a record, not a gate - acting on it is a decision somebody has to make and record elsewhere.'),
        __('Where several check-ups exist for one trainee, nothing marks which is current. The list is ordered by when it was entered, not by the date of the examination.'),
    ],
];
