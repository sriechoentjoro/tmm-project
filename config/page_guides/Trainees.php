<?php
/**
 * Guide for the trainee register and the promotion to apprentice.
 */

return [
    'icon' => 'fa-user-graduate',
    'title' => __('Trainees'),
    'subtitle' => __('The training phase, from the day a candidate is promoted to the day they become an apprentice.'),
    'lead' => __('A trainee is a candidate who was promoted - the same person, with their whole profile copied across and a new TMM code. This is where training happens: they are placed in a batch, tested, given extra courses where needed, and their documents collected. When training is done, the training side promotes them to apprentice, and the profile is copied once more.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Runs the training, records the tests, and promotes a trainee to apprentice.')],
        ['role' => 'administrator', 'can' => __('Everything, without restriction.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads the register to see what became of the candidates it promoted.')],
    ],

    'before' => [
        ['note' => __('A trainee is not created here. They arrive by being promoted from the candidate side, which copies education, experience, certifications, courses and family across.'), 'url' => '/candidates/promote-to-trainee', 'label' => __('Promote to Trainee')],
    ],

    'steps' => [
        [
            'title' => __('Place the trainee in a batch'),
            'who' => __('Training staff'),
            'do' => __('A batch is the cohort: when it starts, how many months it runs, where it trains, and when it is due to depart.'),
            'result' => __('The trainee trains with a group and appears on that batch\'s lists.'),
            'screen' => ['/trainee-training-batches', __('Training Batches')],
        ],
        [
            'title' => __('Record the tests'),
            'who' => __('Training staff'),
            'do' => __('Each test is one trainee, one competency, one score, on one date. The daily screen is the quick way in: pick the date and enter the room\'s scores together.'),
            'result' => __('The scores accumulate, and the averages on the certificate are computed from them.'),
            'screen' => ['/trainee-training-test-scores/daily', __('Daily test scores')],
            'data' => 'trainee_training_test_scores',
        ],
        [
            'title' => __('Add extra training where it is needed'),
            'who' => __('Training staff'),
            'do' => __('A trainee who needs more of something is enrolled into an additional training session - its own course with a date, a place and an instructor.'),
            'result' => __('The enrolment is on file against both the trainee and the session.'),
            'screen' => ['/trainings', __('Trainings')],
        ],
        [
            'title' => __('Collect the documents'),
            'who' => __('Training staff'),
            'do' => __('The trainee checklist shows which required documents are in, and the cost screen records what each document cost and whether it has been paid.'),
            'result' => __('Paperwork readiness is visible without opening every trainee.'),
            'screen' => ['/trainee-submission-documents/checklist', __('Document checklist')],
        ],
        [
            'title' => __('Promote to apprentice'),
            'who' => __('Training staff, or an administrator'),
            'do' => __('The promotion screen lists trainees who have not yet been promoted, with their average test score beside them. Promoting one creates the apprentice and copies education, experience, certifications, courses, family and family stories across.'),
            'result' => __('The person becomes an apprentice and the apprentice phase begins. The trainee record stays, marked as having passed training.'),
            'screen' => ['/trainees/promote-to-apprentice', __('Promote to Apprentice')],
            'data' => 'is_training_pass = 1',
            'note' => __('A trainee who is already an apprentice is refused, so a double click cannot create two of the same person.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Promoted from candidate') . "] --> B[" . __('Placed in a batch') . "]\n"
        . "    B --> C[" . __('Tests recorded') . "]\n"
        . "    B --> D[" . __('Extra training') . "]\n"
        . "    B --> E[" . __('Documents collected') . "]\n"
        . "    C --> F[" . __('Promotion checklist') . "]\n"
        . "    D --> F\n"
        . "    E --> F\n"
        . "    F --> G[" . __('Training promotes') . "]\n"
        . "    G --> H[" . __('Apprentice created, profile copied') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style H fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-clipboard-check',
            'what' => __('The promotion checklist gathers each trainee with the average of their test scores, so readiness is read rather than remembered.'),
            'url' => '/trainees/promotion-checklist',
            'label' => __('Promotion checklist'),
        ],
        [
            'icon' => 'fa-certificate',
            'what' => __('A certificate is issued against the trainee and the batch, and the certificate page computes its own averages from the test scores.'),
            'url' => '/trainee-certificates',
            'label' => __('Certificates'),
        ],
        [
            'icon' => 'fa-plane-departure',
            'what' => __('Promotion creates the apprentice. From there the person is worked on in the apprentice screens - tickets, flights, COE and visa, passport.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
    ],

    'cautions' => [
        __('Promotion copies the profile as it stands. Anything corrected on the trainee afterwards does not follow the apprentice across - correct it on the apprentice instead.'),
        __('The promotion list offers every trainee not yet promoted, whatever their scores. The average beside each name is information, not a gate: the decision is the training side\'s.'),
    ],
];
