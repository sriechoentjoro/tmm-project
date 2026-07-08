<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordMedicalCheckUp $traineeRecordMedicalCheckUp
 * @var array $traineeOptions
 * @var array $resultOptions
 */
$this->assign('title', 'Edit Medical Check-Up #' . $traineeRecordMedicalCheckUp->id);

echo $this->element('TraineeRecordMedicalCheckUps/mcu_form', ['isEdit' => true]);
