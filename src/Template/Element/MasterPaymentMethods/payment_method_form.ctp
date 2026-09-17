<?php
/**
 * Add / edit form for a payment method.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterPaymentMethod $masterPaymentMethod
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $masterPaymentMethod,
    'icon' => 'fa-money-check-alt',
    'title' => $isEdit ? __('Edit Payment Method') : __('Add Payment Method'),
    'subtitle' => __('How a payment reaches the company: transfer, cash, card.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Payment Method'),
    'sections' => [[
        'title' => __('Payment method'),
        'icon' => 'fa-money-check-alt',
        'fields' => [
            'title' => [
                'label' => __('Name'),
                'type' => 'text',
                'required' => true,
                'width' => 12,
                'placeholder' => __('Bank transfer'),
                'help' => __('Chosen when an installment or a receipt is recorded.'),
            ],
        ],
    ]],
]) ?>
