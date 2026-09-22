<?php
/**
 * Guide for user accounts.
 */

return [
    'icon' => 'fa-user-cog',
    'title' => __('Users'),
    'subtitle' => __('The accounts people sign in with, and the roles they hold.'),
    'lead' => __('An account is a username, a password and the roles it holds. Everything about what the person may do comes from those roles, so this screen decides almost nothing on its own - it decides who exists. The two pages every signed-in person can reach whatever their role, their own profile and their own settings, are also here.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Creates accounts, resets them, assigns roles.')],
        ['role' => 'everyone', 'can' => __('Reads and edits their own profile and settings, switches language, signs out.')],
    ],

    'before' => [
        ['note' => __('The roles have to exist before an account can be given one.'), 'url' => '/roles', 'label' => __('Roles')],
    ],

    'steps' => [
        [
            'title' => __('Create the account'),
            'who' => __('Administrator'),
            'do' => __('Give it a username and a password, and say which person it belongs to.'),
            'result' => __('Somebody can sign in - and, until a role is added, reach almost nothing.'),
            'screen' => ['/users/add', __('Add User')],
            'data' => 'users',
        ],
        [
            'title' => __('Give it roles'),
            'who' => __('Administrator'),
            'do' => __('Assign every role the work needs. More than one is normal, and the permissions add up rather than conflict.'),
            'result' => __('The account can do the work.'),
            'screen' => ['/user-roles', __('User Roles')],
        ],
        [
            'title' => __('The person looks after the rest'),
            'who' => __('Anybody signed in'),
            'do' => __('Their own profile and settings are theirs to change, and the language switch is on every page.'),
            'result' => __('Nobody has to ask an administrator to change their own name.'),
            'screen' => ['/users/profile', __('My Profile')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Account created') . "] --> B[" . __('Roles assigned') . "]\n"
        . "    B --> C[" . __('Menus those roles hold') . "]\n"
        . "    C --> D[" . __('What the person may do') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-shield',
            'what' => __('An account with no role can sign in and reach almost nothing. That is not a fault in the account.'),
            'url' => '/user-roles',
            'label' => __('User Roles'),
        ],
        [
            'icon' => 'fa-key',
            'what' => __('What the roles come to is worked out through the menu assignments, and set out in full on the permissions page.'),
            'url' => '/permissions',
            'label' => __('Permissions'),
        ],
    ],

    'cautions' => [
        __('Deleting an account does not remove what that person recorded. Journals, proposals and decisions keep the id of a user who no longer exists, and those screens then show a number instead of a name.'),
        __('Nothing in this application records who changed what. An account is the only trace of who a person is, not of what they did.'),
    ],
];
