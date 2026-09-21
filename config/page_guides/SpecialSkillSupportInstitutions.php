<?php
/**
 * Guide for the registered support institution master.
 */

return [
    'icon' => 'fa-hands-helping',
    'title' => __('Special Skill Support Institutions'),
    'subtitle' => __('The supporting bodies a user account can belong to.'),
    'lead' => __('These are the registered support institutions on the specified-skilled-worker side. In this system their practical role is narrow but important: when a user account is created, it is attached either to one of these institutions or to an LPK, and that attachment is what decides how much of the application that person can see.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Adds, edits and deletes institutions, and attaches user accounts to them.')],
    ],

    'steps' => [
        [
            'title' => __('Record the institution'),
            'who' => __('Administrator'),
            'do' => __('Enter the institution and its contact details.'),
            'result' => __('It becomes selectable on the user form.'),
            'screen' => ['/special-skill-support-institutions/add', __('Add Institution')],
            'data' => 'cms_tmm_stakeholders',
        ],
        [
            'title' => __('Attach a user to it'),
            'who' => __('Administrator'),
            'do' => __('On the user form, choose this institution in the supporting-institution field rather than an LPK.'),
            'result' => __('That user is bound to the institution, and what they can see follows from it.'),
            'screen' => ['/users/add', __('Add User')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Institution on file') . "] --> B[" . __('Chosen on a user account') . "]\n"
        . "    B --> C[" . __('Limits what that user sees') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style C fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-user-shield',
            'what' => __('The user form offers this list beside the LPK list. Which of the two you pick decides which body the account belongs to.'),
            'url' => '/users',
            'label' => __('Users'),
        ],
    ],

    'cautions' => [
        __('Deleting an institution that user accounts are attached to leaves those accounts bound to a record that no longer exists. Move the users first.'),
    ],
];
