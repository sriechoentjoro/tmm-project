<?php
/**
 * Guide for apprentice certificate of eligibility and visa records.
 */

return [
    'icon' => 'fa-stamp',
    'title' => __('COE and Visa'),
    'subtitle' => __('The certificate of eligibility from Japan, and the visa issued against it.'),
    'lead' => __('Going to Japan takes two separate permissions in a fixed order. The acceptance organization applies in Japan for a certificate of eligibility, which is the Japanese side saying the person may come; only then can a visa be applied for at the consulate here, which is the permission to travel. This register keeps the dates of both against the apprentice, plus the type of certificate and where the visa was issued, so the gap between "the certificate arrived" and "the visa arrived" is visible while it matters.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records the certificate and visa dates as they come in.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The passport has to be on file: the certificate is applied for against it and the visa is stamped into it.'), 'url' => '/apprentice-record-pasports', 'label' => __('Passports')],
        ['note' => __('The certificate types are a master list, so the type on the record matches what the paper says.'), 'url' => '/master-apprentice-coe-types', 'label' => __('COE Types')],
    ],

    'steps' => [
        [
            'title' => __('Record the certificate'),
            'who' => __('Apprentice staff'),
            'do' => __('When the certificate of eligibility arrives from Japan, record its type and the date it was received.'),
            'result' => __('The Japanese side has said yes, and the date is on file.'),
            'screen' => ['/apprentice-record-coe-visas/add', __('Add COE and Visa')],
            'data' => 'apprentice_record_coe_visas',
        ],
        [
            'title' => __('Record the visa application'),
            'who' => __('Apprentice staff'),
            'do' => __('Fill the application date on the day the papers go to the consulate.'),
            'result' => __('The clock on the consulate is visible, so a slow case can be chased.'),
        ],
        [
            'title' => __('Record the visa itself'),
            'who' => __('Apprentice staff'),
            'do' => __('When the visa comes back, record the date received and the place it was issued.'),
            'result' => __('The person may travel, and the ticket can be bought against a real date.'),
            'note' => __('A ticket bought before this date is a bet, not a booking.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Passport on file') . "] --> B[" . __('COE received from Japan') . "]\n"
        . "    B --> C[" . __('Visa applied for') . "]\n"
        . "    C --> D[" . __('Visa received') . "]\n"
        . "    D --> E[" . __('Ticket and flight booked') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-passport',
            'what' => __('Both permissions are attached to the passport recorded separately.'),
            'url' => '/apprentice-record-pasports',
            'label' => __('Passports'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('The flight is the step that follows this one, and is the reason the visa date matters to anybody else.'),
            'url' => '/apprentice-flights',
            'label' => __('Flights'),
        ],
    ],

    'cautions' => [
        __('The three dates are plain dates with nothing enforcing their order. A visa recorded as received before it was applied for will be saved without complaint.'),
        __('A certificate of eligibility expires. No expiry is stored here, so a certificate going stale while the visa is delayed is something you have to watch yourself.'),
    ],
];
