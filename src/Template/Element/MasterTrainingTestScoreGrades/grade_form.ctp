<?php
/**
 * Add / edit form for a test score grade.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTrainingTestScoreGrade $masterTrainingTestScoreGrade
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $masterTrainingTestScoreGrade,
    'icon' => 'fa-award',
    'title' => $isEdit ? __('Edit Grade') : __('Add Grade'),
    'subtitle' => __('A band of scores and the grade it earns.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Grade'),
    'sections' => [[
        'title' => __('Grade'),
        'icon' => 'fa-award',
        'fields' => [
            'title' => [
                'label' => __('Grade'),
                'type' => 'text',
                'required' => true,
                'width' => 12,
                'placeholder' => 'A',
                'help' => __('The letter or word written on the score sheet.'),
            ],
            'min_score' => [
                'label' => __('Lowest score'),
                'type' => 'number',
                'step' => 'any',
                'placeholder' => '80',
                'help' => __('A score at or above this value falls in this grade.'),
            ],
            'max_score' => [
                'label' => __('Highest score'),
                'type' => 'number',
                'step' => 'any',
                'placeholder' => '100',
                'help' => __('Keep the bands apart so a score cannot land in two grades.'),
            ],
            'description' => [
                'label' => __('Description'),
                'type' => 'textarea',
                'rows' => 2,
                'width' => 12,
                'help' => __('What the grade means, in words.'),
            ],
        ],
    ]],
]) ?>
