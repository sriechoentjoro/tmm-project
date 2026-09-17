<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterPaymentMethod $masterPaymentMethod
 */
echo $this->element('MasterPaymentMethods/payment_method_form', ['isEdit' => false]);
