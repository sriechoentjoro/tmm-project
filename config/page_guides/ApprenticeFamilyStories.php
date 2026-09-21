<?php
/**
 * Guide for apprentice family stories.
 */

return [
    'icon' => 'fa-users',
    'title' => __('Apprentice Family Stories'),
    'subtitle' => __('What happened to the family at home while the apprentice was away.'),
    'lead' => __('An apprenticeship is three years abroad, and what happens to the family in that time reaches the apprentice whether or not anybody records it - an illness, a death, a debt, a wedding. This register keeps those events in the same shape as the apprentice\'s own stories: the date, a classification, what happened, what was done and what it taught. It is kept separately because the cause is at home while the effect is in Japan, and the two are easier to read apart than mixed together.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Records family events and what was done about them.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The apprentice has to be on file; the story is recorded against the person, not against a named relative.'), 'url' => '/apprentices', 'label' => __('Apprentices')],
        ['note' => __('Who the family are is recorded on the apprentice profile, copied down from the candidate form.'), 'url' => '/apprentice-families', 'label' => __('Apprentice Families')],
    ],

    'steps' => [
        [
            'title' => __('Record the event'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the apprentice, give the date and a title, and classify what kind of event it was.'),
            'result' => __('The event is on file against the apprentice it affects.'),
            'screen' => ['/apprentice-family-stories/add', __('Add Family Story')],
            'data' => 'apprentice_family_stories',
        ],
        [
            'title' => __('Record what was done'),
            'who' => __('Apprentice staff'),
            'do' => __('Describe what happened, the action taken, and what it taught. A supporting photograph or scan can be attached.'),
            'result' => __('The handling of the case is on file, not only the case.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Event at home') . "] --> B[" . __('Family story recorded') . "]\n"
        . "    B --> C[" . __('Action taken and recorded') . "]\n"
        . "    C --> D[" . __('Read alongside the apprentice stories') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style D fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-book-open',
            'what' => __('What happened to the apprentice themselves is the companion register, in the same shape.'),
            'url' => '/apprentice-stories',
            'label' => __('Apprentice Stories'),
        ],
        [
            'icon' => 'fa-home',
            'what' => __('The family members themselves were copied onto the apprentice at promotion, from the candidate record.'),
            'url' => '/apprentice-families',
            'label' => __('Apprentice Families'),
        ],
    ],

    'cautions' => [
        __('The story names the apprentice, not the relative. Which family member it concerns has to be said in the text.'),
        __('Nothing links a family story to the family list. A relative removed from the profile leaves the story standing on its own.'),
    ],
];
