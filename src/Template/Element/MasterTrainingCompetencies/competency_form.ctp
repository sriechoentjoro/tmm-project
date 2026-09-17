<?php
/**
 * Add / edit form for a training competency.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTrainingCompetency $masterTrainingCompetency
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $masterTrainingCompetency,
    'icon' => 'fa-graduation-cap',
    'title' => $isEdit ? __('Edit Competency') : __('Add Competency'),
    'subtitle' => __('A subject trainees are tested on. Test scores and score averages are recorded against it.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Competency'),
    'sections' => [[
        'title' => __('Competency'),
        'icon' => 'fa-graduation-cap',
        'fields' => [
            'title' => [
                'label' => __('Name'),
                'type' => 'text',
                'required' => true,
                'width' => 12,
                'placeholder' => __('Japanese language'),
                'help' => __('Appears on every test score and on the certificate breakdown.'),
            ],
            'description' => [
                'label' => __('Description'),
                'type' => 'textarea',
                'rows' => 3,
                'width' => 12,
                'help' => __('What the competency covers. For the people entering scores.'),
            ],
        ],
    ]],
]) ?>
