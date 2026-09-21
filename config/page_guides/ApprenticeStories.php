<?php
/**
 * Guide for apprentice stories - the history kept while the apprentice is in
 * Japan.
 */

return [
    'icon' => 'fa-book-open',
    'title' => __('Apprentice Stories'),
    'subtitle' => __('What happened to an apprentice in Japan, written down while it is still fresh.'),
    'lead' => __('Once an apprentice has left, the record stops being paperwork and starts being history. A story is one thing that happened and was worth keeping: the day it happened, what kind of problem it was, what actually occurred, what was done about it, and what it taught. The last of those is the reason the page exists - a problem that is written down once can be prepared for by every intake after this one, instead of being met again from scratch.'),

    'actors' => [
        ['role' => 'tmm-apprentice', 'can' => __('Writes stories and keeps the classifications consistent.')],
        ['role' => 'administrator', 'can' => __('Everything.')],
    ],

    'before' => [
        ['note' => __('The apprentice has to be on file. Stories are chosen against the TMM code, not typed as a name.'), 'url' => '/apprentices', 'label' => __('Apprentices')],
    ],

    'steps' => [
        [
            'title' => __('Write down what happened'),
            'who' => __('Apprentice staff'),
            'do' => __('Choose the apprentice and the date it happened, give the story a title that says what it is about, and classify the problem.'),
            'result' => __('The event is on file against the person it happened to.'),
            'screen' => ['/apprentice-stories/add', __('Add Story')],
            'data' => 'apprentice_stories',
            'note' => __('The list counts stories by the exact wording of the classification, so reuse a wording that already exists rather than inventing a near-identical one.'),
        ],
        [
            'title' => __('Record what was done and what it taught'),
            'who' => __('Apprentice staff'),
            'do' => __('Describe the problem, the action taken, and the lesson. A photograph can be attached if there is one.'),
            'result' => __('A story a future intake can be briefed from.'),
        ],
        [
            'title' => __('Read the pattern'),
            'who' => __('Apprentice staff, training staff'),
            'do' => __('The list groups the stories by classification and counts them.'),
            'result' => __('A kind of problem that keeps recurring stops looking like bad luck.'),
            'screen' => ['/apprentice-stories', __('Story List')],
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Something happens in Japan') . "] --> B[" . __('Story recorded') . "]\n"
        . "    B --> C[" . __('Classified') . "]\n"
        . "    C --> D[" . __('Counted by classification') . "]\n"
        . "    B --> E[" . __('Lesson read by the next intake') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style E fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-users',
            'what' => __('What happens to the family at home while the apprentice is away is kept in its own register, in the same shape as this one.'),
            'url' => '/apprentice-family-stories',
            'label' => __('Family Stories'),
        ],
        [
            'icon' => 'fa-user-tie',
            'what' => __('Every story names an apprentice. Deleting the apprentice does not delete their stories, and the story is then attached to nobody.'),
            'url' => '/apprentices',
            'label' => __('Apprentices'),
        ],
    ],

    'cautions' => [
        __('A classification spelled differently is a different classification. "Workplace" and "Work place" are counted as two.'),
        __('Nothing is derived from a story. Recording a serious problem does not raise a flag anywhere else, so anything that needs action needs somebody told as well.'),
    ],
];
