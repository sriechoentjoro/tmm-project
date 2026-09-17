<?php
/**
 * Add / edit form for a trainee's average score in one competency.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeScoreAverage $traineeScoreAverage
 * @var array|\Cake\ORM\Query $trainees
 * @var array|\Cake\ORM\Query $masterTrainingCompetencies
 * @var array|\Cake\ORM\Query $masterTrainingTestScoreGrades
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $traineeScoreAverage,
    'icon' => 'fa-chart-line',
    'title' => $isEdit ? __('Edit Score Average') : __('Add Score Average'),
    'subtitle' => __('One trainee, one competency, and the average they reached in it.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Score Average'),
    'sections' => [[
        'title' => __('Score average'),
        'icon' => 'fa-chart-line',
        'fields' => [
            'trainee_id' => [
                'label' => __('Trainee'),
                'type' => 'select',
                'options' => $trainees,
                'empty' => __('- choose a trainee -'),
                'required' => true,
            ],
            'master_training_competency_id' => [
                'label' => __('Competency'),
                'type' => 'select',
                'options' => $masterTrainingCompetencies,
                'empty' => __('- choose a competency -'),
                'required' => true,
                'help' => __('The subject the average is for.'),
            ],
            'score_average' => [
                'label' => __('Average score'),
                'type' => 'number',
                'step' => 'any',
                'placeholder' => '82.5',
                'help' => __('The mean of the test scores in this competency.'),
            ],
            'master_training_test_score_grade_id' => [
                'label' => __('Grade'),
                'type' => 'select',
                'options' => $masterTrainingTestScoreGrades,
                'empty' => __('- choose a grade -'),
                'help' => __('The band this average falls into.'),
            ],
        ],
    ]],
]) ?>
