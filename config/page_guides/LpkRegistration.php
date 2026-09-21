<?php
/**
 * Guide for the LPK registration screens (Admin prefix).
 *
 * Every statement here is taken from Admin/LpkRegistrationController: the three
 * statuses it writes, the token it generates, the template key it sends, the
 * password rules it enforces, and the activity rows it logs.
 */

return [
    'icon' => 'fa-building',
    'title' => __('LPK Registration'),
    'subtitle' => __('Bringing a vocational training institution into the system, in three steps that are not all yours.'),
    'lead' => __('Registration is a conversation between two sides. You create the institution and the system emails it a verification link; the institution clicks the link and chooses its own password. Only then can its staff log in. An institution stops halfway more often than it finishes in one sitting, so this screen is mostly about seeing where each one is stuck and pushing it along.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Registers an institution, resends the verification email, deletes a registration that was a mistake.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads the list to know which institutions are ready to receive apprentice orders.')],
        ['role' => __('the institution itself'), 'can' => __('Opens the link from the email and sets its own password. It has no account until it does.')],
    ],

    'steps' => [
        [
            'title' => __('Register the institution'),
            'who' => __('Administrator'),
            'do' => __('Fill in the institution name, director, email, username and the MoU file. The email address is where the verification link goes, so it must be one the institution actually reads.'),
            'result' => __('The institution is saved and a verification email is sent. Nobody can log in yet.'),
            'screen' => ['/admin/lpk-registration/create', __('Register LPK')],
            'data' => 'status = pending_verification',
            'note' => __('One email address, one institution. Registering an address that already exists is refused - that is a safeguard, not a fault.'),
        ],
        [
            'title' => __('The institution verifies its email'),
            'who' => __('The institution, from its own inbox'),
            'do' => __('Opens the link in the email. The link carries a 64-character token that works once and expires 24 hours after it was sent.'),
            'result' => __('The email address is confirmed and the institution is sent on to choose a password.'),
            'data' => 'status = verified',
            'note' => __('A link that was already used says so plainly rather than failing: an already-active account is sent to the login page, a verified one to the password screen.'),
        ],
        [
            'title' => __('The institution sets its password'),
            'who' => __('The institution'),
            'do' => __('Chooses a password of at least eight characters containing an upper-case letter, a lower-case letter, a digit and a symbol. The system refuses anything weaker and says which rule failed.'),
            'result' => __('A user account is created for the institution and the registration is complete. From here its staff can log in with the username shown in the email.'),
            'data' => 'status = active',
        ],
        [
            'title' => __('When nothing arrives'),
            'who' => __('Administrator'),
            'do' => __('Use Resend Verification Email on the institution. A new token is generated, valid for another 24 hours, and the previous link stops working.'),
            'result' => __('A fresh email goes to the same address.'),
            'screen' => ['/admin/lpk-registration', __('LPK Registration list')],
            'note' => __('Resending only works while the institution is still pending_verification. Once it is verified or active there is nothing to resend, and the screen will say so.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Administrator registers the LPK') . "] --> B[" . __('Verification email sent') . "]\n"
        . "    B --> C{" . __('Link opened?') . "}\n"
        . "    C -->|" . __('not yet') . "| D[" . __('Resend verification') . "]\n"
        . "    D --> B\n"
        . "    C -->|" . __('yes') . "| E[" . __('Email verified') . "]\n"
        . "    E --> F[" . __('Institution sets its password') . "]\n"
        . "    F --> G[" . __('Account active - can log in') . "]\n"
        . "    G --> H[" . __('Can receive apprentice orders') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style G fill:#c8e6c9\n"
        . "    style H fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-envelope',
            'what' => __('Registering, and resending, both send the lpk_verification email. Its wording, and the letterhead around it, come from the email template of that name - edit the template and every future email changes.'),
            'url' => '/email-templates',
            'label' => __('Email Templates'),
        ],
        [
            'icon' => 'fa-chart-line',
            'what' => __('Registration and activation are written to the stakeholder activity log, which is what the stakeholder dashboard counts.'),
            'url' => '/admin/stakeholder-dashboard',
            'label' => __('Stakeholder Dashboard'),
        ],
        [
            'icon' => 'fa-user-plus',
            'what' => __('Setting the password creates a real user account. From that moment the institution appears in user administration like any other user.'),
            'url' => '/users',
            'label' => __('Users'),
        ],
        [
            'icon' => 'fa-share-alt',
            'what' => __('Only an active institution can be shared an apprentice order. An institution still pending verification will not be offered in the sharing list.'),
            'url' => '/apprentice-orders',
            'label' => __('Apprentice Orders'),
        ],
    ],

    'cautions' => [
        __('An institution sitting at pending_verification has received an email nobody opened. Check the address is right before resending a third time - a typo in the address cannot be seen from this screen, only from the institution never answering.'),
        __('The link is good for 24 hours and for one use. An institution that opens it on the third day is not doing anything wrong - it simply needs a fresh one, which is what Resend Verification Email is for.'),
        __('If the institution forwards the link to a colleague who opens it first, the flow still works: whoever opens it sets the password, and that password belongs to the institution, not to the person.'),
        __('Deleting a registration removes the institution record. If it has already been shared apprentice orders, remove those shares first - they refer to an institution that will no longer exist.'),
    ],
];
