<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordCoeVisa $traineeRecordCoeVisa
 * @var array $traineeOptions
 * @var array $coeTypeOptions
 */
$this->assign('title', 'Edit COE / Visa Record #' . $traineeRecordCoeVisa->id);

echo $this->element('TraineeRecordCoeVisas/coe_visa_form', ['isEdit' => true]);
