<?php
/**
 * Guide for the email templates.
 */

return [
    'icon' => 'fa-envelope',
    'title' => __('Email Templates'),
    'subtitle' => __('The wording of every message the application sends.'),
    'lead' => __('Mail that goes out from the application - an apprentice order shared with institutions, a registration to verify, a notice - takes its subject and body from here rather than from the code. Each template is looked up by a key, and the code asks for the key; that is what lets the wording be changed, or translated, without anybody touching the application.'),

    'actors' => [
        ['role' => 'administrator', 'can' => __('Writes and edits the templates.')],
    ],

    'before' => [
        ['note' => __('Nothing. A template exists before the first message that uses it is sent.')],
    ],

    'steps' => [
        [
            'title' => __('Write the template'),
            'who' => __('Administrator'),
            'do' => __('Give it the key the code asks for, a subject, and a body. Placeholders in the body are filled in when the message is sent.'),
            'result' => __('Messages using that key go out in the new wording.'),
            'screen' => ['/email-templates/add', __('Add Template')],
            'data' => 'email_templates',
        ],
        [
            'title' => __('Change the wording'),
            'who' => __('Administrator'),
            'do' => __('Editing a template changes every message sent from then on, and nothing already sent.'),
            'result' => __('The wording follows the programme without a deployment.'),
        ],
    ],

    'diagram' => "graph LR\n"
        . "    A[" . __('Code asks for a key') . "] --> B[" . __('Template looked up here') . "]\n"
        . "    B --> C[" . __('Placeholders filled in') . "]\n"
        . "    C --> D[" . __('Message sent') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-share-alt',
            'what' => __('Sharing an apprentice order with institutions sends one of these messages to every institution on the list.'),
            'url' => '/apprentice-orders',
            'label' => __('Apprentice Orders'),
        ],
        [
            'icon' => 'fa-school',
            'what' => __('Institution registration sends a verification message the same way.'),
            'url' => '/lpk-registration',
            'label' => __('LPK Registration'),
        ],
    ],

    'cautions' => [
        __('The key is what the code looks for. Renaming or deleting a template leaves the code asking for something that is not there, and the message that depended on it stops going out.'),
        __('A placeholder spelled differently from what the code supplies is left in the message as written, so the recipient sees the placeholder rather than the value.'),
        __('Nothing here records what was sent. Editing a template changes the future only, and there is no copy of the message a recipient actually received.'),
    ],
];
