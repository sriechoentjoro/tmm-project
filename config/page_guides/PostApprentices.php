<?php
/**
 * Guide for the alumni register.
 */

return [
    'icon' => 'fa-user-check',
    'title' => __('Alumni'),
    'subtitle' => __('What an apprentice is doing now that the programme is behind them.'),
    'lead' => __('The last record in the chain. When an apprentice comes home, one row here says when they returned and what became of them: employed, self-employed, still studying, not yet working, or something else. It is the only place the programme can answer what its people did afterwards - which is what a new candidate\'s family asks, and what a partner institution asks before sending anyone.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records returns and keeps the alumni status current.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The apprentice has to exist; alumni are chosen from the apprentice register by TMM code.'), 'url' => '/apprentices', 'label' => __('Apprentices')],
    ],

    'steps' => [
        [
            'title' => __('Record the return'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the apprentice and enter the day they came home from Japan.'),
            'result' => __('The programme knows the apprenticeship is over for this person.'),
            'screen' => ['/post-apprentices/add', __('Add Alumni Record')],
            'data' => 'post_apprentices',
        ],
        [
            'title' => __('Record what they are doing'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the status from the list, and add the employer and position where there is one. Say in the notes how the news reached us.'),
            'result' => __('An answer that can be quoted, with its source.'),
            'note' => __('Choose the status rather than typing one: the summary on the list counts by the exact value.'),
        ],
        [
            'title' => __('Keep it current'),
            'who' => __('Apprentice staff'),
            'do' => __('When you hear that someone has changed job, edit the row rather than adding a second one.'),
            'result' => __('One row per alumnus, holding what is true now.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Apprenticeship ends') . "] --> B[" . __('Return recorded') . "]\n"
        . "    B --> C[" . __('Status recorded') . "]\n"
        . "    C --> D[" . __('Counted in the alumni summary') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-tie',
            'what' => __('The alumnus is the same person as the apprentice, chosen by TMM code. Nothing on the apprentice record changes when an alumni row is added.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('The reports show how many alumni rows exist, as the end of the pipeline funnel.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('Recording a return does not mark the apprenticeship as completed on the apprentice record. The flag the reports read as "completed" is written by nothing at all, so an alumnus can exist while the completed count stays at zero.'),
        __('Nothing stops two alumni rows for one apprentice, and the summary counts both.'),
    ],
];
