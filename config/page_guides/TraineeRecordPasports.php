<?php
/**
 * Guide for trainee passport records.
 */

return [
    'icon' => 'fa-passport',
    'title' => __('Trainee Passports'),
    'subtitle' => __('When the passport was issued, where, when it reached us and when it was paid for.'),
    'lead' => __('The passport is the first of the four departure documents, and the other three lean on it: the certificate of eligibility is applied for against a passport, the visa is stamped into it, and the ticket is issued in the name printed on it. Four dates are kept for each trainee - issued, issued where, received by us, paid - so the difference between "we are waiting for the office" and "we are waiting for the payment" is on the screen rather than in somebody\'s memory.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Records passports and keeps the dates current.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The trainee has to exist, which happens by being promoted from a candidate.'), 'url' => '/trainees', 'label' => __('Trainees')],
    ],

    'steps' => [
        [
            'title' => __('Record the passport'),
            'who' => __('Documentation staff'),
            'do' => __('Choose the trainee, then enter the issue date and the place of issue exactly as printed on the passport.'),
            'result' => __('The passport is on file and the readiness grid ticks its box.'),
            'screen' => ['/trainee-record-pasports/add', __('Add Passport')],
            'data' => 'trainee_record_pasports',
        ],
        [
            'title' => __('Mark when it reached us and when it was paid'),
            'who' => __('Documentation staff'),
            'do' => __('Fill the received date on the day the physical passport arrives, and the paid date when the fee is settled.'),
            'result' => __('A passport still out at the office reads differently from one in the drawer.'),
            'note' => __('The two dates are independent: a passport can be paid for and not yet received, or received and not yet paid.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Passport issued') . "] --> B[" . __('Recorded here') . "]\n"
        . "    B --> C[" . __('COE applied for') . "]\n"
        . "    C --> D[" . __('Visa stamped into it') . "]\n"
        . "    D --> E[" . __('Ticket in the same name') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-clipboard-check',
            'what' => __('The readiness grid ticks the passport column as soon as a row exists here, whether or not its dates are filled in.'),
            'url' => '/trainee-documents/departure',
            'label' => __('Departure Readiness'),
        ],
        [
            'icon' => 'fa-stamp',
            'what' => __('The certificate of eligibility and the visa are recorded separately, and both assume this passport already exists.'),
            'url' => '/trainee-record-coe-visas',
            'label' => __('COE and Visa'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Promotion to apprentice does not copy this record. The apprentice side keeps its own passport register, and the same passport has to be recorded there again.'),
            'url' => '/apprentice-record-pasports',
            'label' => __('Apprentice Passports'),
        ],
    ],

    'cautions' => [
        __('No expiry date is stored, so a passport running out before departure is something you have to watch yourself.'),
        __('One trainee can be given several passport rows. Nothing prevents it and nothing says which is current, so keep to one row per passport.'),
        __('The trainee profile carries a "holds a passport" flag copied down from the candidate form. That is what the person said when they applied, not a link to this register, and the two can disagree.'),
    ],
];
