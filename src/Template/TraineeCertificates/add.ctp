<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeCertificate $traineeCertificate
 * @var array $trainees
 * @var array $batches
 */
echo $this->element('TraineeCertificates/certificate_form', ['isEdit' => false]);
