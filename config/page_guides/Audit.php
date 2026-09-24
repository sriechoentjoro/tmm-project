<?php
/**
 * Guide for the role features page - named Audit, but not a log.
 */

return [
    'icon' => 'fa-clipboard-list',
    'title' => __('Role Features'),
    'subtitle' => __('What your role can reach, and who made which decision.'),
    'lead' => __('Two pages under one name. The features page reads the access chain back for you - it shows the person signed in every screen their roles reach, grouped by category, so the answer to "am I supposed to be able to do this" is on one page instead of being pieced together from the permission grid. The decision trail answers the other question the word audit suggests: who made which call, when, and about whom.'),

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
            'title' => __('Read who decided what'),
            'who' => __('Administrator'),
            'do' => __('The trail lists the decisions in the order they were made, and can be narrowed to one kind. Each line names the person, the roles they held at the time, what they decided and about whom.'),
            'result' => __('A question about a past decision has an answer that does not depend on anybody remembering.'),
            'screen' => ['/audit/trail', __('Decision Trail')],
            'data' => 'logs',
            'note' => __('Twelve moments write a line: putting a candidate forward and taking it back, the two promotions, recording a departure or a completed programme and taking either back, setting an owing cost, and the three permission changes.'),
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
            'what' => __('Everything on the features page comes from the menu assignments. Change one and that page changes with it - and the change itself is written to the trail.'),
            'url' => '/permissions',
            'label' => __('Permissions'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('Recording a departure or the end of a programme writes a line naming who made the call.'),
            'url' => '/apprentices/departure-readiness',
            'label' => __('Departure and Completion'),
        ],
    ],

    'cautions' => [
        __('The trail records decisions, not edits. Correcting a name, a date or an amount on an ordinary form leaves no line - only the twelve moments where somebody decides something do. A trail of every column change would bury the decisions it exists to surface.'),
        __('It starts from the day the table was prepared. Almost nothing before that was recorded anywhere, and what was not is genuinely gone.'),
        __('A few promotions made earlier were copied in from an older table that recorded them. Every such line says so in its detail, and names the record it came from. That marking matters: a line reconstructed afterwards is not worth the same as one written at the moment somebody decided, and a trail that blurs the difference loses what it is for.'),
        __('A line keeps the username and the subject\'s name as text, written at the moment of the decision. That is deliberate: the line still reads correctly after the account or the candidate is deleted, which is exactly when somebody asks.'),
        __('It shows which screens a role reaches, not which actions each assignment grants. A screen listed here can still refuse an add or a delete.'),
    ],
];
