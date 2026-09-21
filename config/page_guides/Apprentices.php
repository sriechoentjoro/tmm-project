<?php
/**
 * Guide for the apprentice register - the third and last copy of the person.
 */

return [
    'icon' => 'fa-user-tie',
    'title' => __('Apprentices'),
    'subtitle' => __('The people who finished training and are on their way to, or already in, Japan.'),
    'lead' => __('An apprentice is a trainee who was promoted, which makes this the third record of the same person: candidate, then trainee, then apprentice. Promotion copies the whole profile across and starts a fresh document trail, because what an apprentice needs on file is different - a passport, a certificate of eligibility and a visa, a medical check-up, a ticket and a flight. Once they are in Japan the record keeps going: what happened to them, what happened to their family at home, and finally what they did after coming back.'),

    'actors' => [
        ['role' => 'tmm-training', 'can' => __('Promotes trainees into this register from the trainee side.')],
        ['role' => 'administrator', 'can' => __('Everything, including the flags the reports count.')],
    ],

    'before' => [
        ['note' => __('Apprentices are not created here. They arrive by being promoted from a trainee, which copies education, experience, certifications, courses and family across.'), 'url' => '/trainees', 'label' => __('Trainees')],
        ['note' => __('The apprentice order says which acceptance organization the person is going to, and how many people that order is for.'), 'url' => '/apprentice-orders', 'label' => __('Apprentice Orders')],
    ],

    'steps' => [
        [
            'title' => __('They arrive by promotion'),
            'who' => __('Training staff'),
            'do' => __('Promotion happens on the trainee page, not here. A new apprentice row is written with the profile copied and a check that the same trainee was not promoted twice.'),
            'result' => __('The person exists as an apprentice, tied back to the trainee and the candidate they came from.'),
            'screen' => ['/trainees', __('Trainees')],
            'data' => 'apprentices',
        ],
        [
            'title' => __('Read the copied profile'),
            'who' => __('Apprentice staff'),
            'do' => __('Open the apprentice and check the profile that was copied, especially the name in katakana, the birth date and the passport name, because these go onto Japanese paperwork exactly as they are here.'),
            'result' => __('The profile that the documents will be written from is the correct one.'),
            'screen' => ['/apprentices', __('Apprentice List')],
            'note' => __('Correcting the trainee record afterwards does not change the apprentice. Correct it here.'),
        ],
        [
            'title' => __('Gather the departure papers'),
            'who' => __('Apprentice staff'),
            'do' => __('Passport, certificate of eligibility and visa, medical check-up, and the submitted documents are each kept in their own register, all pointing back at this apprentice.'),
            'result' => __('Everything needed to leave is on file in one trail.'),
            'screen' => ['/apprentice-submission-documents', __('Submission Documents')],
        ],
        [
            'title' => __('Record the departure'),
            'who' => __('Apprentice staff'),
            'do' => __('The ticket and the flight legs are recorded, so the day and the route are on file rather than in somebody\'s inbox.'),
            'result' => __('The journey is on file.'),
            'screen' => ['/apprentice-flights', __('Flights')],
        ],
        [
            'title' => __('Keep the history while they are there'),
            'who' => __('Apprentice staff'),
            'do' => __('Anything worth highlighting during the apprenticeship is written down as a story - what happened to the apprentice, and what happened to the family at home.'),
            'result' => __('The next intake can be prepared for what this one met.'),
            'screen' => ['/apprentice-stories', __('Apprentice Stories')],
        ],
        [
            'title' => __('Close with an alumni record'),
            'who' => __('Apprentice staff'),
            'do' => __('When they come home, record the return date and what they are doing now.'),
            'result' => __('The programme can say what became of the people who went through it.'),
            'screen' => ['/post-apprentices', __('Alumni')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Trainee promoted') . "] --> B[" . __('Apprentice record created') . "]\n"
        . "    B --> C[" . __('Passport, COE and visa, MCU') . "]\n"
        . "    C --> D[" . __('Ticket and flight') . "]\n"
        . "    D --> E[" . __('In Japan: stories recorded') . "]\n"
        . "    E --> F[" . __('Returned: alumni record') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#fff3e0\n"
        . "    style F fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-graduate',
            'what' => __('The whole record is copied from the trainee at the moment of promotion. Nothing flows between the two afterwards, in either direction.'),
            'url' => '/trainees',
            'label' => __('Trainees'),
        ],
        [
            'icon' => 'fa-file-signature',
            'what' => __('The order this apprentice fills decides the acceptance organization shown on their record, and the order page counts how many of its places are filled.'),
            'url' => '/apprentice-orders',
            'label' => __('Apprentice Orders'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('The reports count apprentices by two flags on this record: one for being in Japan, one for having completed the programme.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('Promotion copies the profile as it stood on that day. A correction made on the trainee afterwards does not follow the person across - make it here instead.'),
        __('The "apprenticeship pass" flag is written as 0 when the record is created, and nothing in the departure screens ever sets it. The reports read it as "in Japan", so that number only moves when somebody edits the apprentice by hand.'),
        __('The "apprentice pass" flag, which the reports show as "completed", is read in three places and written in none. Until something sets it, the completed count stays at zero however many people have finished.'),
        __('Deleting an apprentice leaves their stories, documents and alumni record pointing at a row that is gone. Nothing stops the deletion.'),
    ],
];
