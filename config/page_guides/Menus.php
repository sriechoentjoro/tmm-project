<?php
/**
 * Guide for the menus - the navigation and the permission grid in one.
 */

return [
    'icon' => 'fa-bars',
    'title' => __('Menus'),
    'subtitle' => __('The navigation, and the grid that decides which role sees what.'),
    'lead' => __('This screen does two jobs. It holds the navigation - what appears in the sidebar, under which category, in what order, and which entry is a parent of which - and it holds the permission grid, because a role is given access by being assigned a menu. That is why a screen with no menu entry cannot be granted to anybody: the menu is not a shortcut to the page, it is the handle the permission system holds the page by.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Everything. Nobody else reaches this screen.')],
    ],

    'before' => [
        ['note' => __('The roles have to exist before access can be switched on for them.'), 'url' => '/roles', 'label' => __('Roles')],
    ],

    'steps' => [
        [
            'title' => __('Add the entry'),
            'who' => __('Administrator'),
            'do' => __('Give it a title, an icon, the controller and action it opens, the category it belongs under and where it sits in the order.'),
            'result' => __('The screen has a handle the permission system can hold it by.'),
            'screen' => ['/menus/add', __('Add Menu')],
            'data' => 'menus',
        ],
        [
            'title' => __('Switch it on for a role'),
            'who' => __('Administrator'),
            'do' => __('The grid has a cell per menu per role. Switching it on creates the assignment; switching it off leaves the assignment behind but grants nothing.'),
            'result' => __('The role can reach the screen.'),
            'data' => 'role_menus',
        ],
        [
            'title' => __('Say which actions it grants'),
            'who' => __('Administrator'),
            'do' => __('Each assignment takes a list of actions. Empty or * grants the menu\'s own action plus index and view; an explicit list such as index,view,add,edit,delete grants exactly that.'),
            'result' => __('The role can do the work, not only look at it.'),
            'note' => __('A role that can open a list but cannot add to it is almost always an assignment left at * when it needed an explicit list.'),
        ],
        [
            'title' => __('Arrange it'),
            'who' => __('Administrator'),
            'do' => __('An entry can be moved to another category or under another parent, and switched off entirely without being deleted.'),
            'result' => __('The sidebar reads the way the work runs.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Menu entry') . "] --> B[" . __('Appears in the sidebar') . "]\n"
        . "    A --> C[" . __('Assigned to a role') . "]\n"
        . "    C --> D[" . __('Granted actions on the assignment') . "]\n"
        . "    D --> E[" . __('What the role may do') . "]\n"
        . "    A --> F{" . __('Has children for the same role?') . "}\n"
        . "    F -->|" . __('yes') . "| G[" . __('Container only: grants nothing') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9\n"
        . "    style G fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-key',
            'what' => __('The whole access chain, from the user to the action, is set out on the permissions page.'),
            'url' => '/permissions',
            'label' => __('Permissions'),
        ],
        [
            'icon' => 'fa-clipboard-list',
            'what' => __('What each role can actually reach, read back from these assignments, is what the features page shows them.'),
            'url' => '/audit',
            'label' => __('Role Features'),
        ],
    ],

    'cautions' => [
        __('A menu with at least one active child assigned to the same role is treated as a navigation container: it grants nothing itself, and the children carry the permissions. Assigning only the parent grants nothing at all.'),
        __('Switching a menu off removes it from everybody at once, whatever their role assignments say.'),
        __('The menus live in one database and the role assignments in another. A menu deleted here leaves its assignments behind, pointing at an entry that is gone.'),
        __('An entry whose controller and action do not match a real screen saves without complaint and fails only when somebody clicks it.'),
    ],
];
