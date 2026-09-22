<?php
/**
 * Guide for trainee certificate of eligibility and visa records.
 */

return [
    'icon' => 'fa-stamp',
    'title' => __('Trainee COE and Visa'),
    'subtitle' => __('The certificate of eligibility from Japan, and the visa issued against it.'),
    'lead' => __('Two separate permissions, in a fixed order. The receiving side applies in Japan for a certificate of eligibility, which is Japan saying the person may come; only then can a visa be applied for at the consulate here, which is the permission to travel. This register keeps the dates of both against the trainee, plus the type of certificate and where the visa was issued, so the gap between "the certificate arrived" and "the visa arrived" is visible while there is still time to act on it.'),

    'actors' => [
        ['role' => 'tmm-documentation', 'can' => __('Records the certificate and visa dates as they come in.')],
        ['role' => 'administrator', 'can' => __('Everything, including the certificate types.')],
    ],

    'before' => [
        ['note' => __('The passport has to be on file: the certificate is applied for against it and the visa is stamped into it.'), 'url' => '/trainee-record-pasports', 'label' => __('Passports')],
    ],

    'steps' => [
        [
            'title' => __('Record the certificate'),
            'who' => __('Documentation staff'),
            'do' => __('When the certificate of eligibility arrives from Japan, record its type and the date it was received.'),
            'result' => __('The Japanese side has said yes, and the date is on file.'),
            'screen' => ['/trainee-record-coe-visas/add', __('Add COE and Visa')],
            'data' => 'trainee_record_coe_visas',
        ],
        [
            'title' => __('Record the visa application'),
            'who' => __('Documentation staff'),
            'do' => __('Fill the application date on the day the papers go to the consulate.'),
            'result' => __('The time the consulate is taking becomes visible, so a slow case can be chased.'),
        ],
        [
            'title' => __('Record the visa itself'),
            'who' => __('Documentation staff'),
            'do' => __('When the visa comes back, record the date received and the place it was issued.'),
            'result' => __('The person may travel, and a ticket can be bought against a real date.'),
            'note' => __('A ticket bought before this date is a bet, not a booking.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Passport on file') . "] --> B[" . __('COE received from Japan') . "]\n"
        . "    B --> C[" . __('Visa applied for') . "]\n"
        . "    C --> D[" . __('Visa received') . "]\n"
        . "    D --> E[" . __('Ticket booked') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-ticket-alt',
            'what' => __('The ticket is the step that follows, and the visa date is the reason its date can be trusted.'),
            'url' => '/tickets',
            'label' => __('Tickets'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Promotion to apprentice does not copy this record either. The apprentice side has its own COE and visa register.'),
            'url' => '/apprentice-record-coe-visas',
            'label' => __('Apprentice COE and Visa'),
        ],
    ],

    'cautions' => [
        __('The three dates are plain dates with nothing enforcing their order. A visa recorded as received before it was applied for saves without complaint.'),
        __('A certificate of eligibility expires. No expiry is stored here, so a certificate going stale while the visa is delayed is yours to watch.'),
    ],
];
