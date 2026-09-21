<?php
/**
 * Guide for the stakeholder dashboard.
 */

return [
    'icon' => 'fa-chart-line',
    'title' => __('Stakeholder Dashboard'),
    'subtitle' => __('One view of where every partner institution stands.'),
    'lead' => __('This screen counts rather than edits. It reads the institution register and the activity log that registration writes to, and turns them into the question an administrator actually has: how many partners are usable today, and how many are stuck part-way through joining. Nothing here changes data - every number is a link back to the screen that can.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Reads every figure and exports the statistics.')],
    ],

    'before' => [
        ['note' => __('The figures are only as complete as the register behind them. An institution registered outside the registration flow is counted, but has no activity history to show.'), 'url' => '/admin/lpk-registration', 'label' => __('LPK Registration')],
    ],

    'steps' => [
        [
            'title' => __('Read the state of the partnership'),
            'who' => __('Administrator'),
            'do' => __('The counts separate institutions that can log in from those still pending verification or verified but without a password.'),
            'result' => __('You know where to spend the afternoon: chasing, or nothing.'),
            'screen' => ['/admin/stakeholder-dashboard', __('Stakeholder Dashboard')],
            'data' => 'stakeholder_activities',
        ],
        [
            'title' => __('Export the statistics'),
            'who' => __('Administrator'),
            'do' => __('The export produces the same figures as a file.'),
            'result' => __('Something to attach to a report.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('LPK registration') . "] --> B[" . __('Activity log') . "]\n"
        . "    C[" . __('Institution register') . "] --> D[" . __('Dashboard counts') . "]\n"
        . "    B --> D\n"
        . "    D --> E[" . __('Who needs chasing') . "]\n"
        . "    style D fill:#e3f2fd\n"
        . "    style E fill:#fff3e0",

    'triggers' => [
        [
            'icon' => 'fa-envelope',
            'what' => __('A pending institution is chased from the registration screen, which resends its verification email.'),
            'url' => '/admin/lpk-registration',
            'label' => __('LPK Registration'),
        ],
        [
            'icon' => 'fa-school',
            'what' => __('Every count here can be opened as a list on the institution register.'),
            'url' => '/vocational-training-institutions',
            'label' => __('LPK list'),
        ],
    ],

    'cautions' => [
        __('A figure that looks wrong is usually a registration that bypassed the normal flow rather than a fault in the counting. Check the institution on the register before doubting the dashboard.'),
    ],
];
