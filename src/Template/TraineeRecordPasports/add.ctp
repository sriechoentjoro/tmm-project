<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordPasport $traineeRecordPasport
 * @var array $traineeOptions
 * @var int $preselectedTrainee
 */
$this->assign('title', 'Add Passport Record');

echo $this->element('TraineeRecordPasports/passport_form', [
    'isEdit' => false,
    'preselectedTrainee' => isset($preselectedTrainee) ? $preselectedTrainee : 0,
]);
