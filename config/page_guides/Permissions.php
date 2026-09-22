<?php
/**
 * Guide for the permissions overview - and the one place the whole access
 * chain is written down.
 */

return [
    'icon' => 'fa-key',
    'title' => __('Permissions'),
    'subtitle' => __('Which role can reach which screen, and how that is worked out.'),
    'lead' => __('There is no table of permissions in this application. What a person may do is worked out from a chain: the user has roles, a role is assigned menus, and each of those assignments carries a list of the actions it grants. This page is the overview of that chain - it shows what each role can reach and lets one role\'s assignments be copied to another. Understanding the chain matters, because a screen that refuses somebody is almost never a bug in that screen.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Everything. An administrator is allowed every action without the chain being consulted at all.')],
    ],

    'before' => [
        ['note' => __('The roles have to exist before anything can be assigned to them.'), 'url' => '/roles', 'label' => __('Roles')],
        ['note' => __('The menus are what gets assigned; a screen with no menu entry cannot be granted to anybody.'), 'url' => '/menus', 'label' => __('Menus')],
    ],

    'steps' => [
        [
            'title' => __('The user gets a role'),
            'who' => __('Administrator'),
            'do' => __('A person can hold more than one role, and what they may do is everything their roles allow put together.'),
            'result' => __('The account is tied to one or more roles.'),
            'screen' => ['/user-roles', __('User Roles')],
            'data' => 'user_roles',
        ],
        [
            'title' => __('The role gets menus'),
            'who' => __('Administrator'),
            'do' => __('On the menu screen, each menu can be switched on or off for each role. An assignment that is switched off grants nothing.'),
            'result' => __('The role can reach the screens those menus point at.'),
            'screen' => ['/menus', __('Menus')],
            'data' => 'role_menus',
        ],
        [
            'title' => __('The assignment says which actions'),
            'who' => __('Administrator'),
            'do' => __('Each assignment carries a list of granted actions. Leave it empty, or write *, and the role gets the menu\'s own action plus index and view - browsing and reading, nothing else. Write an explicit list such as index,view,add,edit,delete to grant more.'),
            'result' => __('The role can do exactly what the list says.'),
            'note' => __('This is the step people miss. * does not mean every action: it means the menu\'s action, index and view. A role that needs to add or delete has to be given an explicit list.'),
        ],
        [
            'title' => __('Copy a role that already works'),
            'who' => __('Administrator'),
            'do' => __('Choose a role to copy from and a role to copy to. Every menu the first role has switched on is assigned to the second, with the same granted actions.'),
            'result' => __('A new role starts from a working set instead of from nothing.'),
            'screen' => ['/permissions', __('Permissions')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('User') . "] --> B[" . __('Roles they hold') . "]\n"
        . "    B --> C[" . __('Menus assigned to the role') . "]\n"
        . "    C --> D{" . __('Granted actions on the assignment') . "}\n"
        . "    D -->|" . __('empty or *') . "| E[" . __('Menu action, index, view') . "]\n"
        . "    D -->|" . __('explicit list') . "| F[" . __('Exactly those actions') . "]\n"
        . "    E --> G{" . __('Does the role reach that database?') . "}\n"
        . "    F --> G\n"
        . "    G -->|" . __('yes') . "| H[" . __('Allowed') . "]\n"
        . "    G -->|" . __('no') . "| I[" . __('Refused') . "]\n"
        . "    style D fill:#fff3e0\n"
        . "    style H fill:#c8e6c9\n"
        . "    style I fill:#ffcdd2",

    'triggers' => [
        [
            'icon' => 'fa-bars',
            'what' => __('A menu that has children assigned to the same role is treated as a navigation container and grants nothing itself - the children carry the real permissions. Assigning only the parent therefore grants nothing at all.'),
            'url' => '/menus',
            'label' => __('Menus'),
        ],
        [
            'icon' => 'fa-user-shield',
            'what' => __('The roles themselves are a short list, and the name is what the code tests against. Renaming a role breaks every check written against the old name.'),
            'url' => '/roles',
            'label' => __('Roles'),
        ],
    ],

    'cautions' => [
        __('Copying carries the granted actions across with the menu, so a role copied from one that could add, edit and delete can do the same. The message afterwards says how many of the copied assignments carry an explicit list, which is the quickest way to see that the copy took.'),
        __('A menu the target role already has is skipped, switched off or not, and the message says how many were skipped. Copying onto a role whose assignment was deliberately switched off leaves it switched off - that is on purpose, so a copy cannot undo a decision somebody made.'),
        __('Beyond the chain there is a second gate: a role that has no access to the database a screen reads from is refused even when the menu says yes.'),
        __('A few actions are allowed to everybody signed in, whatever the chain says: their own profile and settings, the language switch, logging out, and the "?" guide page on every module.'),
    ],
];
