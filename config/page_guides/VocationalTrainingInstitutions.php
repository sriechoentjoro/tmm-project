<?php
/**
 * Guide for the LPK (vocational training institution) management screens.
 */

return [
    'icon' => 'fa-school',
    'title' => __('Vocational Training Institutions (LPK)'),
    'subtitle' => __('The register of partner institutions, and the state each one is in.'),
    'lead' => __('An LPK is the partner that finds and trains candidates in Indonesia. This screen is the register of them: who they are, whether their account works, and which ones are ready to be given apprentice orders. Registration itself happens on the LPK Registration screen - here you read the result and repair what is stuck.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Sees every institution, edits, deletes, resends a verification email.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads the register to decide which institutions to share an apprentice order with.')],
        ['role' => 'lpk-penyangga', 'can' => __('Sees only its own institution. The filter is applied in the controller, not in the page, so the restriction holds however the page is reached.')],
    ],

    'before' => [
        ['note' => __('An institution appears here only after it has been registered, which is a separate screen with its own three-step flow.'), 'url' => '/admin/lpk-registration', 'label' => __('LPK Registration')],
    ],

    'steps' => [
        [
            'title' => __('Read the status of each institution'),
            'who' => __('Administrator or recruitment staff'),
            'do' => __('The list shows the registration state of every institution. Three values matter: pending_verification means the email was sent and never acted on, verified means the address was confirmed but no password chosen, active means the institution can log in.'),
            'result' => __('You know which institutions are usable and which are waiting on someone.'),
            'screen' => ['/vocational-training-institutions', __('LPK list')],
            'data' => 'vocational_training_institutions.status',
        ],
        [
            'title' => __('Open one institution'),
            'who' => __('Administrator or recruitment staff'),
            'do' => __('The detail page gathers what is on file: contact details, the MoU document, and the account state.'),
            'result' => __('Enough to decide whether to send this institution an apprentice order, or to chase it.'),
            'data' => 'is_registered, registered_at, email_verified_at',
        ],
        [
            'title' => __('Push a stuck registration along'),
            'who' => __('Administrator'),
            'do' => __('Resend Verification Email issues a new link and invalidates the old one.'),
            'result' => __('The institution receives a fresh email at the same address.'),
            'note' => __('If an institution has been pending for days, the address is the first thing to doubt, not the email system. Check it before resending again.'),
        ],
        [
            'title' => __('Export the register'),
            'who' => __('Administrator'),
            'do' => __('CSV, Excel and PDF exports produce the same list you are looking at, filters included.'),
            'result' => __('A file for reporting outside the system.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Registered') . "] --> B[" . __('Verified') . "]\n"
        . "    B --> C[" . __('Active') . "]\n"
        . "    C --> D[" . __('Receives apprentice orders') . "]\n"
        . "    D --> E[" . __('Proposes candidates') . "]\n"
        . "    style C fill:#c8e6c9\n"
        . "    style E fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-share-alt',
            'what' => __('An active institution becomes selectable when an apprentice order is shared. Sharing sends it the apprentice_order_shared email and it can then propose candidates.'),
            'url' => '/apprentice-orders',
            'label' => __('Apprentice Orders'),
        ],
        [
            'icon' => 'fa-users',
            'what' => __('Users of the lpk-penyangga role are attached to one institution, and that attachment is what limits everything they can see across the application.'),
            'url' => '/users',
            'label' => __('Users'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Candidates carry the institution that proposed them, so deleting an institution leaves its candidates pointing at nothing.'),
            'url' => '/candidates',
            'label' => __('Candidates'),
        ],
        [
            'icon' => 'fa-chart-line',
            'what' => __('The counts on the stakeholder dashboard are drawn from this register and its activity log.'),
            'url' => '/admin/stakeholder-dashboard',
            'label' => __('Stakeholder Dashboard'),
        ],
    ],

    'cautions' => [
        __('Deleting an institution is not undone by re-registering it: candidates, shares and users that referred to it keep an id that no longer resolves. Prefer leaving a dormant institution on file.'),
        __('An institution that never verified its email has no account at all. It will not appear in any login report, and its staff cannot be helped by resetting a password that does not exist - resend the verification instead.'),
    ],
];
