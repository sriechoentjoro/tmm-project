<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordPasport $traineeRecordPasport
 * @var array $traineeOptions
 */
$this->assign('title', 'Edit Passport Record #' . $traineeRecordPasport->id);

echo $this->element('TraineeRecordPasports/passport_form', ['isEdit' => true]);
