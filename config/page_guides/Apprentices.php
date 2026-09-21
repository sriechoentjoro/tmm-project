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
        ['role' => 'tmm-training', 'can' => __('Promotes trainees into this register, and makes the two calls at the end: that somebody has left for Japan, and that their programme is finished.')],
        ['role' => 'tmm-documentation', 'can' => __('Collects the departure documents and runs the pre-departure check-up. Training hears both before deciding.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
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
            'title' => __('Record the journey'),
            'who' => __('Apprentice staff'),
            'do' => __('The ticket and the flight legs are recorded, so the day and the route are on file rather than in somebody\'s inbox.'),
            'result' => __('The journey is on file.'),
            'screen' => ['/apprentice-flights', __('Flights')],
        ],
        [
            'title' => __('Training says they have left'),
            'who' => __('TMM Training'),
            'do' => __('Training weighs three things: its own test results, summarised in the certificate; whether documentation has the departure papers; and what the pre-departure check-up came to. The departure screen shows all three beside each apprentice, and the call is made there.'),
            'result' => __('The apprentice counts as being in Japan, in this screen and in the reports.'),
            'screen' => ['/apprentices/departure-readiness', __('Departure and Completion')],
            'data' => 'apprentices.is_apprenticeship_pass',
            'note' => __('A check-up marked not fit refuses the call outright. Everything else is training\'s judgement, not a rule the screen enforces.'),
        ],
        [
            'title' => __('Keep the history while they are there'),
            'who' => __('TMM Training'),
            'do' => __('Training watches the apprenticeship as it runs and writes down anything worth highlighting as a story - what happened to the apprentice, and what happened to the family at home.'),
            'result' => __('The next intake can be prepared for what this one met.'),
            'screen' => ['/apprentice-stories', __('Apprentice Stories')],
        ],
        [
            'title' => __('Training says the programme is finished'),
            'who' => __('TMM Training'),
            'do' => __('When the apprenticeship ends, training records it on the same screen. Only somebody already recorded as having left can be recorded as finished.'),
            'result' => __('The apprentice counts as completed in the reports.'),
            'screen' => ['/apprentices/departure-readiness', __('Departure and Completion')],
            'data' => 'apprentices.is_apprentice_pass',
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
        . "    D --> E{" . __('Training weighs the evidence') . "}\n"
        . "    E -->|" . __('certificate, documents, check-up') . "| F[" . __('Recorded as in Japan') . "]\n"
        . "    E -->|" . __('check-up not fit') . "| G[" . __('Refused until that changes') . "]\n"
        . "    F --> H[" . __('Stories recorded during the programme') . "]\n"
        . "    H --> I[" . __('Recorded as completed') . "]\n"
        . "    I --> J[" . __('Returned: alumni record') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#fff3e0\n"
        . "    style G fill:#ffcdd2\n"
        . "    style J fill:#c8e6c9",

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
            'what' => __('The reports count apprentices by the two flags training sets here: one for being in Japan, one for having completed the programme.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('Promotion copies the profile as it stood on that day. A correction made on the trainee afterwards does not follow the person across - make it here instead.'),
        __('Both calls are training\'s alone. Buying a ticket, recording a flight or writing an alumni record changes neither of them - if the reports show nobody in Japan, it is because nobody has made the call on the departure screen.'),
        __('Taking a departure back also takes the completion back, because a programme that never started cannot have finished.'),
        __('Deleting an apprentice leaves their stories, documents and alumni record pointing at a row that is gone. Nothing stops the deletion.'),
    ],
];
