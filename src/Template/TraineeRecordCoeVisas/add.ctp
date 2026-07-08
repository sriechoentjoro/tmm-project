<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordCoeVisa $traineeRecordCoeVisa
 * @var array $traineeOptions
 * @var array $coeTypeOptions
 * @var int $preselectedTrainee
 */
$this->assign('title', 'Add COE / Visa Record');

echo $this->element('TraineeRecordCoeVisas/coe_visa_form', [
    'isEdit' => false,
    'preselectedTrainee' => isset($preselectedTrainee) ? $preselectedTrainee : 0,
]);
