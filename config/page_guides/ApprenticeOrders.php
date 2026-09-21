<?php
/**
 * Guide for apprentice orders - where the candidate phase begins.
 */

return [
    'icon' => 'fa-file-signature',
    'title' => __('Apprentice Orders'),
    'subtitle' => __('The request from Japan that sets the whole recruitment in motion.'),
    'lead' => __('An apprentice order is a Japanese company asking for a number of apprentices: which job, how many men and women, when they should depart, and what else is required of them. Nothing in recruitment happens before one exists. Once it does, you share it with the partner institutions that should go looking, and each of them receives it by email.'),

    'actors' => [
        ['role' => 'tmm-recruitment', 'can' => __('Raises an order, shares it with institutions, withdraws a share.')],
        ['role' => 'administrator', 'can' => __('The same, without restriction.')],
        ['role' => __('everyone else'), 'can' => __('Reads orders. The add, edit, delete, share and withdraw actions are refused in the controller, so the restriction holds even for a hand-typed address.')],
    ],

    'before' => [
        ['note' => __('An order names the cooperative that will supervise it. It has to exist before the order can be raised.'), 'url' => '/cooperative-associations', 'label' => __('Cooperative Associations')],
        ['note' => __('An order names the company that will accept the apprentices. It has to exist too.'), 'url' => '/acceptance-organizations', 'label' => __('Acceptance Organizations')],
        ['note' => __('Only institutions that finished registration can be shared an order. Check the register first if the one you want is not offered.'), 'url' => '/vocational-training-institutions', 'label' => __('LPK list')],
    ],

    'steps' => [
        [
            'title' => __('Raise the order'),
            'who' => __('Recruitment staff'),
            'do' => __('Record the job category, how many men and women are wanted, the departure month and year, and any other requirement the company has set. Those are the fields the email to the institutions is built from, so what you write here is what they will read.'),
            'result' => __('The order exists and can be shared.'),
            'screen' => ['/apprentice-orders/add', __('New Apprentice Order')],
            'data' => 'apprentice_orders',
        ],
        [
            'title' => __('Share it with the institutions'),
            'who' => __('Recruitment staff'),
            'do' => __('Choose one institution, several, or all of them. Sharing is deliberate and selective - an order is not broadcast on its own.'),
            'result' => __('Each chosen institution is recorded as a share and receives the apprentice_order_shared email at its registered address. From that moment it can start proposing candidates against this order.'),
            'data' => "apprentice_order_shares.status = 'shared'",
            'note' => __('Sharing the same order with an institution twice does not duplicate it - the existing share is reused and its details refreshed.'),
        ],
        [
            'title' => __('Withdraw a share when the situation changes'),
            'who' => __('Recruitment staff'),
            'do' => __('Withdraw the order from one institution. The share is not deleted; it is marked withdrawn, so the history of who was asked and when survives.'),
            'result' => __('The institution receives the apprentice_order_cancelled email. Candidates it already proposed are not removed.'),
            'data' => "apprentice_order_shares.status = 'cancelled'",
            'note' => __('Withdrawing twice is harmless: the screen says the share was already withdrawn and sends nothing.'),
        ],
        [
            'title' => __('Watch what comes back'),
            'who' => __('Recruitment staff'),
            'do' => __('Institutions answer by registering candidates. The candidate list is where the answers arrive.'),
            'result' => __('Recruitment can see who has been proposed, by which institution, against which order.'),
            'screen' => ['/candidates', __('Candidates')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Cooperative + accepting company on file') . "] --> B[" . __('Order raised') . "]\n"
        . "    B --> C[" . __('Shared with chosen LPKs') . "]\n"
        . "    C --> D[" . __('Email to each LPK') . "]\n"
        . "    D --> E[" . __('LPK recruits and registers candidates') . "]\n"
        . "    C -.->|" . __('situation changes') . "| F[" . __('Share withdrawn, LPK told') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9\n"
        . "    style F fill:#ffebee",

    'triggers' => [
        [
            'icon' => 'fa-envelope',
            'what' => __('Sharing sends apprentice_order_shared and withdrawing sends apprentice_order_cancelled. Both are templates you can edit - the wording that reaches the institutions is not fixed in the code.'),
            'url' => '/email-templates',
            'label' => __('Email Templates'),
        ],
        [
            'icon' => 'fa-school',
            'what' => __('The email goes to the address the institution registered with. If it has changed address since, fix it on the register before sharing.'),
            'url' => '/vocational-training-institutions',
            'label' => __('LPK list'),
        ],
        [
            'icon' => 'fa-user-plus',
            'what' => __('An institution answers an order by registering candidates against it, which is how recruitment work reaches the candidate screens.'),
            'url' => '/candidates',
            'label' => __('Candidates'),
        ],
    ],

    'cautions' => [
        __('The share email carries the numbers and requirements as they stood when you shared. Editing the order afterwards does not re-send anything, so an institution can be working from the old figures - share again, or tell them.'),
        __('Withdrawing a share does not withdraw the candidates that institution already proposed. Those remain, and remain yours to decide about.'),
    ],
];
