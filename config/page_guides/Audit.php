<?php
/**
 * Guide for the role features page - named Audit, but not a log.
 */

return [
    'icon' => 'fa-clipboard-list',
    'title' => __('Role Features'),
    'subtitle' => __('What the role you are signed in as can actually reach.'),
    'lead' => __('The access chain is easy to get wrong and hard to read back: roles, menus, assignments, granted actions. This page reads it back for you. It shows the person signed in every screen their roles reach, grouped by category, so the answer to "am I supposed to be able to do this" is on one page instead of being pieced together from the permission grid.'),

    'actors' => [
        ['role' => 'everyone', 'can' => __('Sees what their own roles reach.')],
        ['role' => 'administrator', 'can' => __('Sees everything, because an administrator reaches everything.')],
    ],

    'before' => [
        ['note' => __('It reads the menu assignments, so it is only as right as they are.'), 'url' => '/permissions', 'label' => __('Permissions')],
    ],

    'steps' => [
        [
            'title' => __('Read what you can reach'),
            'who' => __('Anybody signed in'),
            'do' => __('The features are grouped by category, with the menus your roles hold under each.'),
            'result' => __('A straight answer about your own access, without asking an administrator.'),
            'screen' => ['/audit', __('Role Features')],
        ],
        [
            'title' => __('Take a gap back to the grid'),
            'who' => __('Administrator'),
            'do' => __('A screen somebody expected and cannot see is a missing assignment, or an assignment switched off, or a parent menu assigned without its children.'),
            'result' => __('The gap is fixed where it lives.'),
            'screen' => ['/menus', __('Menus')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Menu assignments') . "] --> B[" . __('Read back per role') . "]\n"
        . "    B --> C[" . __('Grouped by category') . "]\n"
        . "    C --> D[" . __('What you can reach') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-key',
            'what' => __('Everything shown here comes from the menu assignments. Change one and this page changes with it.'),
            'url' => '/permissions',
            'label' => __('Permissions'),
        ],
    ],

    'cautions' => [
        __('This page is called Audit, and it is not an audit trail. It answers what a role can reach, not what anybody did.'),
        __('Nothing in this application records who changed what. There is a table for it and no code writes to it, so there is no history to look up - a wrong figure can be corrected but not traced.'),
        __('It shows which screens a role reaches, not which actions each assignment grants. A screen listed here can still refuse an add or a delete.'),
    ],
];
