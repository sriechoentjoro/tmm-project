<?php
/**
 * Guide for assigning roles to people.
 */

return [
    'icon' => 'fa-user-lock',
    'title' => __('User Roles'),
    'subtitle' => __('Which person holds which role.'),
    'lead' => __('The join between the two halves of the access system: accounts on one side, roles on the other. A person can hold several roles and their permissions add up - somebody who is both documentation and accounting reaches both sets of screens. Nothing here grants anything directly; it decides which roles a person speaks with, and the roles carry the access.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Assigns and removes roles.')],
    ],

    'before' => [
        ['note' => __('The account has to exist.'), 'url' => '/users', 'label' => __('Users')],
        ['note' => __('The role has to exist and have its menus assigned, or holding it changes nothing.'), 'url' => '/permissions', 'label' => __('Permissions')],
    ],

    'steps' => [
        [
            'title' => __('Assign the role'),
            'who' => __('Administrator'),
            'do' => __('Choose the person and tick the roles their work needs.'),
            'result' => __('They can reach everything those roles reach, from their next page load.'),
            'screen' => ['/user-roles', __('User Roles')],
            'data' => 'user_roles',
        ],
        [
            'title' => __('Take one away'),
            'who' => __('Administrator'),
            'do' => __('Removing a role removes the access that came with it, and leaves any other roles the person holds untouched.'),
            'result' => __('The person keeps the rest of their work.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Person') . "] --> B[" . __('Role A') . "]\n"
        . "    A --> C[" . __('Role B') . "]\n"
        . "    B --> D[" . __('Everything both roles reach') . "]\n"
        . "    C --> D\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-shield',
            'what' => __('Several decisions in the pipeline are gated by role name, so adding or removing a role changes who can make them, not only what they can see.'),
            'url' => '/roles',
            'label' => __('Roles'),
        ],
    ],

    'cautions' => [
        __('Roles add up and never cancel out. There is no way to give somebody a role and take one screen away from them again; the narrowest set that covers the work is the right one.'),
        __('Granting the administrator role bypasses the whole menu system for that person. It is not a slightly wider role - it is every action in the application.'),
    ],
];
