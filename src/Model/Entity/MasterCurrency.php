<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterCurrency Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $currency_code
 * @property string|null $country
 *
 * @property \App\Model\Entity\TraineeInstallment[] $trainee_installments
 */
class MasterCurrency extends Entity
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
        'title' => true,
        'currency_code' => true,
        'country' => true,
        'trainee_installments' => true,
    ];
}
