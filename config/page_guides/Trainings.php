<?php
/**
 * Guide for additional training sessions.
 */

return [
    'icon' => 'fa-chalkboard-user',
    'title' => __('Trainings'),
    'subtitle' => __('Extra sessions a trainee can be enrolled into, beside the batch they belong to.'),
    'lead' => __('A batch is the cohort a trainee trains with; a training is a single session with its own title, dates, place, instructor and a limit on how many can attend. It is what you use when somebody needs more of something - a language push before departure, a skill the tests showed was weak - without moving them out of their batch.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Opens sessions and enrols trainees into them.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'steps' => [
        [
            'title' => __('Open the session'),
            'who' => __('Training staff'),
            'do' => __('Give it a title, its start and end dates, where it is held, who teaches it, and the maximum number of participants.'),
            'result' => __('Trainees can be enrolled into it.'),
            'screen' => ['/trainings/add', __('New Training')],
            'data' => 'trainings',
        ],
        [
            'title' => __('Enrol the trainees who need it'),
            'who' => __('Training staff'),
            'do' => __('Enrolment is done from the trainee side, against the session.'),
            'result' => __('The enrolment is on file for both, and can be changed or withdrawn later.'),
            'screen' => ['/trainees', __('Trainees')],
        ],
        [
            'title' => __('Follow a batch whose location changed'),
            'who' => __('Training staff'),
            'do' => __('The location-shift screen gathers the batches whose training location has moved, with their original and moved schedules side by side.'),
            'result' => __('Where each group is actually training is visible in one place.'),
            'screen' => ['/trainings/location-shift', __('Location shift')],
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Session opened') . "] --> B[" . __('Trainees enrolled') . "]\n"
        . "    B --> C[" . __('Attendance and outcome') . "]\n"
        . "    D[" . __('Batch') . "] -.->|" . __('separate from') . "| A\n"
        . "    style A fill:#e3f2fd\n"
        . "    style C fill:#c8e6c9",

    'triggers' => [
        ['icon' => 'fa-user-graduate', 'what' => __('Enrolment happens from the trainee, so a trainee\'s page is where their extra sessions are listed.'), 'url' => '/trainees', 'label' => __('Trainees')],
        ['icon' => 'fa-layer-group', 'what' => __('A training session is not a batch. The batch is the cohort and the schedule; this is one extra session inside it.'), 'url' => '/trainee-training-batches', 'label' => __('Training Batches')],
    ],

    'cautions' => [
        __('The participant limit is recorded but nothing enforces it - enrolling more people than the room holds is a decision, not an error the system will stop.'),
    ],
];
