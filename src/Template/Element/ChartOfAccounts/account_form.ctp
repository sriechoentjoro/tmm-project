<?php
/**
 * Add / edit form for a chart-of-accounts entry.
 *
 * The type used to be a free text box. Five values are the only ones the rest
 * of the accounting pages understand - ChartOfAccountsController::index() filters
 * on exactly this list and hierarchy() groups by it - so a typo here quietly
 * dropped the account out of both. It is a dropdown now.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChartOfAccount $chartOfAccount
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;

$types = [
    'Asset' => __('Asset'),
    'Liability' => __('Liability'),
    'Equity' => __('Equity'),
    'Revenue' => __('Revenue'),
    'Expense' => __('Expense'),
];
?>
<?= $this->element('entity_form', [
    'entity' => $chartOfAccount,
    'icon' => 'fa-book',
    'title' => $isEdit ? __('Edit Account') : __('Add Account'),
    'subtitle' => __('An account in the chart of accounts. Journal lines are posted against it.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Account'),
    'sections' => [
        [
            'title' => __('Account'),
            'icon' => 'fa-book',
            'fields' => [
                'code' => [
                    'label' => __('Account code'),
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => '1-1000',
                    'help' => __('The list is ordered by this code, so keep the numbering consistent.'),
                ],
                'name' => [
                    'label' => __('Account name'),
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => __('Cash on hand'),
                    'help' => __('Shown on every journal line that touches this account.'),
                ],
                'type' => [
                    'label' => __('Type'),
                    'type' => 'select',
                    'options' => $types,
                    'empty' => __('- choose a type -'),
                    'required' => true,
                    'help' => __('The account hierarchy and the totals are grouped by type.'),
                ],
                'is_active' => [
                    'label' => __('Active'),
                    'type' => 'checkbox',
                    'help' => __('An inactive account stays on file but should no longer be posted to.'),
                ],
                'description' => [
                    'label' => __('Description'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                    'help' => __('What belongs in this account, for whoever posts the journal.'),
                ],
            ],
        ],
    ],
]) ?>
