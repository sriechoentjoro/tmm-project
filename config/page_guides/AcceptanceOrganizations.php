<?php
/**
 * Guide for the accepting-organisation master.
 */

return [
    'icon' => 'fa-industry',
    'title' => __('Acceptance Organizations'),
    'subtitle' => __('The Japanese companies that accept apprentices.'),
    'lead' => __('An acceptance organization is the company in Japan that will take the apprentices. It is a master list: kept once, chosen many times. Its importance is not in this screen but in the screens that cannot work without it - an apprentice order names the accepting company, and so do candidates, trainees and apprentices as they move through the pipeline.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Adds, edits and deletes organisations.')],
        ['role' => 'tmm-recruitment', 'can' => __('Chooses one when raising an apprentice order.')],
    ],

    'steps' => [
        [
            'title' => __('Record the organisation before it is needed'),
            'who' => __('Administrator'),
            'do' => __('Enter the company and its contact details. This is a master record, so keep the name exactly as it should appear on every order and document that will cite it.'),
            'result' => __('The organisation becomes selectable everywhere it is required.'),
            'screen' => ['/acceptance-organizations/add', __('Add Acceptance Organization')],
            'data' => 'acceptance_organizations',
        ],
        [
            'title' => __('Keep its history'),
            'who' => __('Administrator or recruitment staff'),
            'do' => __('Stories recorded against an organisation collect what happened with it over time - problems met, how they were handled.'),
            'result' => __('The next person choosing this company can see what the last one learned.'),
            'screen' => ['/acceptance-organization-stories', __('Acceptance Organization Stories')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Acceptance organization on file') . "] --> B[" . __('Named on an apprentice order') . "]\n"
        . "    B --> C[" . __('Carried by the candidate') . "]\n"
        . "    C --> D[" . __('Carried by the trainee') . "]\n"
        . "    D --> E[" . __('Carried by the apprentice in Japan') . "]\n"
        . "    style A fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-file-signature',
            'what' => __('An apprentice order cannot name an accepting company that is not on this list. If the one you need is missing, it has to be added here first.'),
            'url' => '/apprentice-orders/add',
            'label' => __('New Apprentice Order'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Candidates, trainees and apprentices each carry the accepting organisation, which is how a person can be traced back to the company that accepted them.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
    ],

    'cautions' => [
        __('Renaming an organisation changes it everywhere at once, including on orders already raised. That is usually what you want; be sure it is.'),
        __('Deleting one that is already named on an order leaves that order pointing at a company that no longer exists on file.'),
    ],
];
