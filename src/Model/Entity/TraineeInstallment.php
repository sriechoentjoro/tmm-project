<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeInstallment Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property int $master_transaction_category_id
 * @property int $payment_amount
 * @property \Cake\I18n\FrozenDate $payment_date
 * @property int $full_payment_amount
 * @property int $payment_accummulated
 * @property int $unpaid_amount
 * @property int $master_currency_id
 * @property bool $is_paid_off
 *
 * @property \App\Model\Entity\Trainee $trainee
 * @property \App\Model\Entity\MasterTransactionCategory $master_transaction_category
 * @property \App\Model\Entity\MasterCurrency $master_currency
 */
class TraineeInstallment extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'trainee_id' => true,
        'master_transaction_category_id' => true,
        'payment_amount' => true,
        'payment_date' => true,
        'full_payment_amount' => true,
        'payment_accummulated' => true,
        'unpaid_amount' => true,
        'master_currency_id' => true,
        'is_paid_off' => true,
        'trainee' => true,
        'master_transaction_category' => true,
        'master_currency' => true,
    ];
}
