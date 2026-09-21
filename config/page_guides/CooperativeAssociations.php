<?php
/**
 * Guide for the cooperative (kumiai) master.
 */

return [
    'icon' => 'fa-handshake',
    'title' => __('Cooperative Associations'),
    'subtitle' => __('The kumiai that supervises the apprenticeship in Japan.'),
    'lead' => __('A cooperative association is the supervising body between the sending side and the accepting company. Like the accepting organisations, this is a master list whose real weight is felt elsewhere: an apprentice order names both the cooperative and the accepting company, and the order cannot be raised until both exist here.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Adds, edits and deletes cooperatives.')],
        ['role' => 'tmm-recruitment', 'can' => __('Chooses one when raising an apprentice order.')],
    ],

    'steps' => [
        [
            'title' => __('Record the cooperative'),
            'who' => __('Administrator'),
            'do' => __('Enter the cooperative and its contact details, with the name spelled as it should appear on every order that cites it.'),
            'result' => __('The cooperative becomes selectable when an apprentice order is raised.'),
            'screen' => ['/cooperative-associations/add', __('Add Cooperative Association')],
            'data' => 'cooperative_associations',
        ],
        [
            'title' => __('Keep its history'),
            'who' => __('Administrator or recruitment staff'),
            'do' => __('Stories against a cooperative record how the relationship has actually gone.'),
            'result' => __('Experience stays with the record instead of with whoever happened to be there.'),
            'screen' => ['/cooperative-association-stories', __('Cooperative Association Stories')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Cooperative on file') . "] --> B[" . __('Named on an apprentice order') . "]\n"
        . "    C[" . __('Acceptance organization on file') . "] --> B\n"
        . "    B --> D[" . __('Order shared with LPKs') . "]\n"
        . "    style B fill:#fff3e0\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-file-signature',
            'what' => __('Together with the accepting organisation, the cooperative is what an apprentice order is built on. Both must exist before an order can be raised.'),
            'url' => '/apprentice-orders/add',
            'label' => __('New Apprentice Order'),
        ],
    ],

    'cautions' => [
        __('Two records for the same cooperative, differing only in spelling, will split its orders into two groups that no report puts back together. Search before adding.'),
    ],
];
