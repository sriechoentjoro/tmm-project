<?php
/**
 * Edit form for an apprentice story.
 *
 * A story is a problem an apprentice ran into in Japan, written down so the
 * next intake does not meet it unprepared: what happened, how it was
 * classified, what was done, and what it taught.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeStory $apprenticeStory
 * @var array $apprenticeOptions
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? true;
?>
<?= $this->element('entity_form', [
    'entity' => $apprenticeStory,
    'icon' => 'fa-book-open',
    'title' => $isEdit ? __('Edit Story') : __('Add Story'),
    'subtitle' => __('A problem an apprentice met, and what came of it.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Story'),
    'sections' => [
        [
            'title' => __('The story'),
            'icon' => 'fa-book-open',
            'fields' => [
                'apprentice_id' => [
                    'label' => __('Apprentice'),
                    'type' => 'select',
                    'options' => $apprenticeOptions,
                    'empty' => __('- choose an apprentice -'),
                    'required' => true,
                    'help' => __('Who this happened to.'),
                ],
                'date_occurrence' => [
                    'label' => __('When it happened'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                ],
                'title' => [
                    'label' => __('Title'),
                    'type' => 'text',
                    'required' => true,
                    'width' => 8,
                    'help' => __('A line that says what the story is about.'),
                ],
                'problem_classification' => [
                    'label' => __('Classification'),
                    'type' => 'text',
                    'width' => 4,
                    'placeholder' => __('Workplace'),
                    'help' => __('The list page counts stories by this exact wording, so reuse one that already exists.'),
                ],
            ],
        ],
        [
            'title' => __('What happened, and what came of it'),
            'icon' => 'fa-lightbulb',
            'fields' => [
                'problem_contents' => [
                    'label' => __('What happened'),
                    'type' => 'textarea',
                    'rows' => 4,
                    'width' => 12,
                ],
                'problem_solution' => [
                    'label' => __('What was done'),
                    'type' => 'textarea',
                    'rows' => 4,
                    'width' => 12,
                ],
                'problem_inference' => [
                    'label' => __('What it taught'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                    'help' => __('The part a future intake reads.'),
                ],
                'image_path' => [
                    'label' => __('Image path'),
                    'type' => 'text',
                    'width' => 12,
                    'placeholder' => 'files/uploads/apprentice_stories/…',
                    'help' => __('A path to an image already on the server. This field stores text, it does not upload.'),
                ],
            ],
        ],
    ],
]) ?>
