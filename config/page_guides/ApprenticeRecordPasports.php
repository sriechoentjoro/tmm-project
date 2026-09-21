<?php
/**
 * Guide for apprentice passport records.
 */

return [
    'icon' => 'fa-passport',
    'title' => __('Apprentice Passports'),
    'subtitle' => __('When the passport was issued, where, when it reached us and when it was paid for.'),
    'lead' => __('Nothing else in the departure chain can start without a passport: the certificate of eligibility is applied for against a passport, and the visa is stamped into it. This register keeps four dates for each apprentice - issued, issued where, received by us, and paid - so the question "are we waiting on the passport, or on the payment?" has an answer on the screen rather than in somebody\'s memory.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records passports and keeps the dates current.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The apprentice has to exist first, which happens by promotion from the trainee side.'), 'url' => '/apprentices', 'label' => __('Apprentices')],
    ],

    'steps' => [
        [
            'title' => __('Record the passport'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the apprentice and enter the issue date and the place it was issued, exactly as printed on the passport.'),
            'result' => __('The passport is on file and the visa application can quote it.'),
            'screen' => ['/apprentice-record-pasports/add', __('Add Passport')],
            'data' => 'apprentice_record_pasports',
        ],
        [
            'title' => __('Mark when it reached us'),
            'who' => __('Apprentice staff'),
            'do' => __('Fill the received date on the day the physical passport arrives, and the paid date when the fee is settled.'),
            'result' => __('A passport still out at the office is visibly different from one in the drawer.'),
            'note' => __('The two dates are independent: a passport can be paid for and not yet received, or received and not yet paid.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Passport issued') . "] --> B[" . __('Recorded here') . "]\n"
        . "    B --> C[" . __('COE applied for') . "]\n"
        . "    C --> D[" . __('Visa stamped into it') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-stamp',
            'what' => __('The certificate of eligibility and the visa are recorded separately, but both assume the passport here already exists.'),
            'url' => '/apprentice-record-coe-visas',
            'label' => __('COE and Visa'),
        ],
        [
            'icon' => 'fa-user-tie',
            'what' => __('The apprentice profile carries a "holds a passport" flag copied down from the candidate form. It is a statement the person made at application time, not a link to this register, and the two can disagree.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
    ],

    'cautions' => [
        __('Nothing here checks the passport against its expiry, and no expiry date is stored. A passport that runs out before the apprenticeship ends will not be flagged by this page.'),
        __('One apprentice can be given several passport rows. Nothing prevents it, and nothing says which is the current one, so keep to one row per passport.'),
    ],
];
