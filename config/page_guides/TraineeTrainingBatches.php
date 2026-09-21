<?php
/**
 * Guide for training batches.
 */

return [
    'icon' => 'fa-layer-group',
    'title' => __('Training Batches'),
    'subtitle' => __('The cohort a trainee trains with, and where and when it happens.'),
    'lead' => __('A batch is a group of trainees trained together on one schedule: how many months it runs, where it trains, and when the group is due to depart for Japan. It also carries the case where the training location changes part-way through, which is why it keeps both an original location and a moved one.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Creates batches and maintains their schedule.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'steps' => [
        [
            'title' => __('Open the batch'),
            'who' => __('Training staff'),
            'do' => __('Give it a name, the training term in months, the original training location, and the planned start, finish and departure dates.'),
            'result' => __('Trainees can be placed in it and certificates can cite it.'),
            'screen' => ['/trainee-training-batches/add', __('New Batch')],
            'data' => 'training_batches',
        ],
        [
            'title' => __('Record a change of location'),
            'who' => __('Training staff'),
            'do' => __('When the training moves, mark the batch as moved and fill in the new location with its own start and finish dates. The original dates are kept rather than overwritten.'),
            'result' => __('Both the plan and what actually happened stay on file.'),
            'data' => 'is_training_location_moved',
            'note' => __('Overwriting the original location instead of marking the move loses the fact that it ever changed.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Batch opened') . "] --> B[" . __('Trainees placed in it') . "]\n"
        . "    B --> C[" . __('Tests and training') . "]\n"
        . "    C --> D[" . __('Certificates cite the batch') . "]\n"
        . "    A -.->|" . __('if it moves') . "| E[" . __('Moved location recorded') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        ['icon' => 'fa-user-graduate', 'what' => __('Trainees are placed into a batch, and the batch appears wherever they are listed.'), 'url' => '/trainees', 'label' => __('Trainees')],
        ['icon' => 'fa-certificate', 'what' => __('A certificate names the batch a trainee finished, so a batch deleted afterwards leaves certificates pointing at nothing.'), 'url' => '/trainee-certificates', 'label' => __('Certificates')],
    ],

    'cautions' => [
        __('A batch with trainees in it should not be deleted. Close it instead: the trainees, their scores and their certificates all refer back to it.'),
    ],
];
