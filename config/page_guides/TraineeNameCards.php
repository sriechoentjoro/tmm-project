<?php
/**
 * Guide for trainee name cards.
 */

return [
    'icon' => 'fa-id-badge',
    'title' => __('Trainee Name Cards'),
    'subtitle' => __('The printed card a trainee wears, for one person or for a whole batch.'),
    'lead' => __('The one screen here that produces paper rather than records. It reads the trainees and their batches as they already stand and lays them out for printing - a single card when somebody joins late or loses theirs, or every card in a batch at once when a new intake starts. Nothing is stored: printing the same card twice produces the same card, and correcting a name means correcting the trainee.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Prints cards for a batch as it starts.')],
        ['role' => 'tmm-documentation', 'can' => __('Prints a replacement when one is needed.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The card carries the name, the TMM code and the batch, so all three have to be right on the trainee first.'), 'url' => '/trainees', 'label' => __('Trainees')],
        ['note' => __('A trainee with no batch prints with the batch line empty.'), 'url' => '/trainee-training-batches', 'label' => __('Training Batches')],
    ],

    'steps' => [
        [
            'title' => __('Choose who to print for'),
            'who' => __('Training staff'),
            'do' => __('The list shows every trainee with their batch, and every batch with how many trainees are in it.'),
            'result' => __('You can print one card or a whole intake from the same page.'),
            'screen' => ['/trainee-name-cards', __('Name Cards')],
        ],
        [
            'title' => __('Print'),
            'who' => __('Training staff'),
            'do' => __('The card opens in a print layout with no menu around it, ready for the printer.'),
            'result' => __('A card that matches what is on file at the moment it was printed.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Trainee and batch on file') . "] --> B[" . __('Card laid out') . "]\n"
        . "    B --> C[" . __('Printed') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style C fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Everything on the card comes from the trainee record. A name corrected there changes the next card printed, and nothing at all about the cards already handed out.'),
            'url' => '/trainees',
            'label' => __('Trainees'),
        ],
        [
            'icon' => 'fa-layer-group',
            'what' => __('The batch printed on the card is the one the trainee is placed in.'),
            'url' => '/trainee-training-batches',
            'label' => __('Training Batches'),
        ],
    ],

    'cautions' => [
        __('Nothing records that a card was printed. There is no list of who has one and no way to tell a reprint from a first print.'),
        __('The cards are laid out from live data, so a batch printed today and the same batch printed next week can differ without anybody deciding to change them.'),
    ],
];
