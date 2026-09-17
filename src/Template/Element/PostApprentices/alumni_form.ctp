<?php
/**
 * Add / edit form for an alumnus record - where an apprentice went after
 * returning from Japan.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PostApprentice $postApprentice
 * @var array $apprenticeOptions
 * @var array $statusOptions
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $postApprentice,
    'icon' => 'fa-user-check',
    'title' => $isEdit ? __('Edit Alumni Record') : __('Add Alumni Record'),
    'subtitle' => __('What an apprentice is doing now that the programme is behind them.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Record'),
    'sections' => [
        [
            'title' => __('Who, and since when'),
            'icon' => 'fa-user-check',
            'fields' => [
                'apprentice_id' => [
                    'label' => __('Apprentice'),
                    'type' => 'select',
                    'options' => $apprenticeOptions,
                    'empty' => __('- choose an apprentice -'),
                    'required' => true,
                    'help' => __('The person this record follows.'),
                ],
                'return_date' => [
                    'label' => __('Return date'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                    'help' => __('The day they came home from Japan.'),
                ],
            ],
        ],
        [
            'title' => __('What they are doing now'),
            'icon' => 'fa-briefcase',
            'fields' => [
                'current_status' => [
                    'label' => __('Current status'),
                    'type' => 'select',
                    'options' => $statusOptions,
                    'empty' => __('- choose a status -'),
                    'help' => __('The alumni summary counts by this exact value, so choose rather than type.'),
                ],
                'employer' => [
                    'label' => __('Employer'),
                    'type' => 'text',
                    'help' => __('Where they work, if they do.'),
                ],
                'position' => [
                    'label' => __('Position'),
                    'type' => 'text',
                    'width' => 12,
                    'help' => __('The job title they hold.'),
                ],
                'notes' => [
                    'label' => __('Notes'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                    'help' => __('How the news reached us, or anything else worth keeping.'),
                ],
            ],
        ],
    ],
]) ?>
