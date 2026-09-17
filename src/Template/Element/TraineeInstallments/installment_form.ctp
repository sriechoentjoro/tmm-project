<?php
/**
 * Edit form for a trainee's installment payment.
 *
 * The four money fields are not independent: payment_accummulated and
 * unpaid_amount are the running totals the add form calculates as payments come
 * in, and is_paid_off is what the list and the receipt read to decide whether an
 * account is settled. Editing one without the others leaves the record
 * disagreeing with itself, which is why they carry the warnings they do.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment $traineeInstallment
 * @var array|\Cake\ORM\Query $trainees
 * @var array|\Cake\ORM\Query $masterTransactionCategories
 * @var array|\Cake\ORM\Query $masterCurrencies
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? true;
?>
<?= $this->element('entity_form', [
    'entity' => $traineeInstallment,
    'icon' => 'fa-file-invoice-dollar',
    'title' => $isEdit ? __('Edit Installment') : __('Add Installment'),
    'subtitle' => __('One payment towards what a trainee owes.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Add Installment'),
    'sections' => [
        [
            'title' => __('Who paid, and for what'),
            'icon' => 'fa-user',
            'fields' => [
                'trainee_id' => [
                    'label' => __('Trainee'),
                    'type' => 'select',
                    'options' => $trainees,
                    'empty' => __('- choose a trainee -'),
                    'required' => true,
                    'help' => __('Moving a payment to another trainee changes both their balances.'),
                ],
                'master_transaction_category_id' => [
                    'label' => __('Category'),
                    'type' => 'select',
                    'options' => $masterTransactionCategories,
                    'empty' => __('- choose a category -'),
                    'help' => __('What the payment is for. The payment report groups by it.'),
                ],
            ],
        ],
        [
            'title' => __('This payment'),
            'icon' => 'fa-money-bill-wave',
            'fields' => [
                'payment_amount' => [
                    'label' => __('Amount paid'),
                    'type' => 'number',
                    'step' => 'any',
                    'help' => __('What was handed over on this occasion.'),
                ],
                'payment_date' => [
                    'label' => __('Payment date'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                ],
                'master_currency_id' => [
                    'label' => __('Currency'),
                    'type' => 'select',
                    'options' => $masterCurrencies,
                    'empty' => __('- choose a currency -'),
                    'help' => __('The currency the amounts on this row are in.'),
                ],
            ],
        ],
        [
            'title' => __('The balance'),
            'subtitle' => __('These three are totals, not this payment. Change them only to correct a total that is already wrong.'),
            'icon' => 'fa-calculator',
            'fields' => [
                'full_payment_amount' => [
                    'label' => __('Total owed'),
                    'type' => 'number',
                    'step' => 'any',
                    'width' => 4,
                    'help' => __('The whole sum the trainee has to pay.'),
                ],
                'payment_accummulated' => [
                    'label' => __('Paid so far'),
                    'type' => 'number',
                    'step' => 'any',
                    'width' => 4,
                    'help' => __('Everything received up to and including this payment.'),
                ],
                'unpaid_amount' => [
                    'label' => __('Still owed'),
                    'type' => 'number',
                    'step' => 'any',
                    'width' => 4,
                    'help' => __('Total owed minus paid so far.'),
                ],
                'is_paid_off' => [
                    'label' => __('Settled'),
                    'type' => 'checkbox',
                    'width' => 12,
                    'help' => __('The list and the receipt read this flag, not the numbers above it.'),
                ],
            ],
        ],
    ],
]) ?>
