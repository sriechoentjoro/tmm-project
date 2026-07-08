<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordMedicalCheckUp $traineeRecordMedicalCheckUp
 * @var array $traineeOptions
 * @var array $resultOptions
 * @var int $preselectedTrainee
 */
$this->assign('title', 'Add Medical Check-Up');

echo $this->element('TraineeRecordMedicalCheckUps/mcu_form', [
    'isEdit' => false,
    'preselectedTrainee' => isset($preselectedTrainee) ? $preselectedTrainee : 0,
]);
