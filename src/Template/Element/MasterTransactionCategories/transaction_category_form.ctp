<?php
/**
 * Add / edit form for a transaction category.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTransactionCategory $masterTransactionCategory
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $masterTransactionCategory,
    'icon' => 'fa-tags',
    'title' => $isEdit ? __('Edit Transaction Category') : __('Add Transaction Category'),
    'subtitle' => __('What a payment is for. Trainee installments are grouped by it.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Category'),
    'sections' => [[
        'title' => __('Category'),
        'icon' => 'fa-tags',
        'fields' => [
            'title' => [
                'label' => __('Name'),
                'type' => 'text',
                'required' => true,
                'width' => 12,
                'placeholder' => __('Training fee'),
                'help' => __('Shown on the installment form and in the payment reports.'),
            ],
        ],
    ]],
]) ?>
