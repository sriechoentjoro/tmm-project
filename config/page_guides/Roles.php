<?php
/**
 * Guide for the roles.
 */

return [
    'icon' => 'fa-user-shield',
    'title' => __('Roles'),
    'subtitle' => __('The short list of names the whole access system is built on.'),
    'lead' => __('A role is little more than a name, and that is the point: everything else hangs off it. Menus are assigned to roles, not to people; the code that decides who may make a decision - who may put a candidate forward, who may record a departure - tests the role name; and a person holds as many roles as their work needs. Because the name is what is tested, the names here are not labels that can be reworded freely.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('Nothing. Roles come first; menus and users are attached to them afterwards.')],
    ],

    'steps' => [
        [
            'title' => __('Create the role'),
            'who' => __('Administrator'),
            'do' => __('Give it a name and a description that says what the role is for.'),
            'result' => __('There is something to assign menus and people to.'),
            'screen' => ['/roles/add', __('Add Role')],
            'data' => 'roles',
        ],
        [
            'title' => __('Give it access'),
            'who' => __('Administrator'),
            'do' => __('Assign the menus it needs, and say which actions each assignment grants. Copying an existing role is faster than starting empty.'),
            'result' => __('The role can reach its work.'),
            'screen' => ['/permissions', __('Permissions')],
        ],
        [
            'title' => __('Give it to people'),
            'who' => __('Administrator'),
            'do' => __('Assign the role to the accounts that need it.'),
            'result' => __('Those people can do the work.'),
            'screen' => ['/user-roles', __('User Roles')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Role created') . "] --> B[" . __('Menus assigned to it') . "]\n"
        . "    A --> C[" . __('People assigned to it') . "]\n"
        . "    B --> D[" . __('What those people may do') . "]\n"
        . "    C --> D\n"
        . "    style A fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-key',
            'what' => __('Access is worked out through the role, never against the person directly.'),
            'url' => '/permissions',
            'label' => __('Permissions'),
        ],
        [
            'icon' => 'fa-user-lock',
            'what' => __('Decisions in the pipeline are gated by role name: the proposing institution puts a candidate forward, recruitment promotes, training records a departure and the end of a programme.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
    ],

    'cautions' => [
        __('The name is what the code tests, not the description. Renaming a role silently breaks every check written against the old name, and those checks fail closed - the button simply stops working for that role.'),
        __('The administrator role is special: it is allowed every action without the menu assignments being consulted at all. Testing a change by signing in as an administrator proves nothing about anybody else.'),
        __('Deleting a role leaves its menu assignments and its people behind. The people lose the access silently.'),
    ],
];
