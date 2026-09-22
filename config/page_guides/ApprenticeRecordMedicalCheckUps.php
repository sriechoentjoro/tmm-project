<?php
/**
 * Guide for apprentice medical check-ups.
 */

return [
    'icon' => 'fa-stethoscope',
    'title' => __('Apprentice Medical Check-Ups'),
    'subtitle' => __('The health check an apprentice takes before leaving, and the result on file.'),
    'lead' => __('An apprentice is examined again before departure: the check taken as a candidate is months old by then, and the receiving side asks for a current one. Each record names the clinic, the date, the result chosen from the shared master list, a comment, and the scanned file. Several records per apprentice are normal here, because a re-check after treatment is a second examination, not a correction of the first.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records check-ups and attaches the results.')],
        ['role' => 'administrator', 'can' => __('Everything, including the master list of results.')],
    ],

    'before' => [
        ['note' => __('The result values come from the shared master list, the same one the candidate check-ups use.'), 'url' => '/master-medical-check-up-results', 'label' => __('MCU Results')],
    ],

    'steps' => [
        [
            'title' => __('Record the examination'),
            'who' => __('Apprentice staff'),
            'do' => __('Enter the apprentice, the clinic, the date and a title that says which examination this was.'),
            'result' => __('The examination is on file with the clinic that performed it.'),
            'screen' => ['/apprentice-record-medical-check-ups/add', __('Add Medical Check-Up')],
            'data' => 'apprentice_record_medical_check_ups',
        ],
        [
            'title' => __('Choose the result'),
            'who' => __('Apprentice staff'),
            'do' => __('Pick the result from the master list and attach the scan. Put anything the result does not cover in the comment.'),
            'result' => __('The result is a value the system can count, not a sentence somebody has to read.'),
            'note' => __('Each result in the master list is marked fit or not fit. Saving the check-up works the apprentice\'s standing out from that marking straight away: any result marked not fit makes the standing "not fit", and that is what TMM Training sees on the departure screen.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Examination at the clinic') . "] --> B[" . __('Result recorded here') . "]\n"
        . "    B --> C[" . __('Scan attached') . "]\n"
        . "    C --> D[" . __('Read by the departure paperwork') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-sliders-h',
            'what' => __('The result values, and whether each one counts as fit, are maintained in one master list used by both the candidate and the apprentice check-ups.'),
            'url' => '/master-medical-check-up-results',
            'label' => __('MCU Results'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('The standing worked out here is one of the three things TMM Training weighs before recording a departure, and the only one that refuses it outright.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
        [
            'icon' => 'fa-file-medical',
            'what' => __('The check-up taken during selection is a separate record on the candidate, and is what decided whether they could be promoted at all.'),
            'url' => '/candidate-record-medical-check-ups',
            'label' => __('Candidate Medical Check-Ups'),
        ],
    ],

    'cautions' => [
        __('A result marked "not fit" refuses a departure until it changes. Nothing else happens automatically - the apprentice is not rejected, and nobody is notified, so anything beyond the departure still needs somebody told.'),
        __('Where several check-ups exist for one apprentice, nothing marks which is the current one. The list is ordered by when it was entered, not by the date of the examination.'),
        __('Changing what a result means, on the master screen, re-checks everybody who ever received it. A result switched to not fit can therefore stop people who were passing this morning, and the screen says how many.'),
    ],
];
