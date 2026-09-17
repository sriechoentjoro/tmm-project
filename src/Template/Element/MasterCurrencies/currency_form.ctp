<?php
/**
 * Add / edit form for a currency.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterCurrency $masterCurrency
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $masterCurrency,
    'icon' => 'fa-coins',
    'title' => $isEdit ? __('Edit Currency') : __('Add Currency'),
    'subtitle' => __('Currencies are offered wherever an amount of money is recorded.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Currency'),
    'sections' => [[
        'title' => __('Currency'),
        'icon' => 'fa-coins',
        'fields' => [
            'title' => [
                'label' => __('Name'),
                'type' => 'text',
                'required' => true,
                'placeholder' => __('Japanese Yen'),
                'help' => __('The name shown in every currency dropdown.'),
            ],
            'currency_code' => [
                'label' => __('Code'),
                'type' => 'text',
                'maxlength' => 3,
                'placeholder' => 'JPY',
                'help' => __('The three-letter ISO code: JPY, IDR, USD.'),
            ],
            'country' => [
                'label' => __('Country'),
                'type' => 'text',
                'width' => 12,
                'placeholder' => __('Japan'),
                'help' => __('Where the currency is issued. For reference only.'),
            ],
        ],
    ]],
]) ?>
