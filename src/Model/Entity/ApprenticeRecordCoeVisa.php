<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeRecordCoeVisa Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property int $master_coe_type_id
 * @property \Cake\I18n\FrozenDate $date_coe_received
 * @property \Cake\I18n\FrozenDate $date_visa_application
 * @property \Cake\I18n\FrozenDate $date_visa_received
 * @property string $place_visa_issued
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 * @property \App\Model\Entity\MasterCoeType $master_coe_type
 */
class ApprenticeRecordCoeVisa extends Entity
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
        'apprentice_id' => true,
        'master_coe_type_id' => true,
        'date_coe_received' => true,
        'date_visa_application' => true,
        'date_visa_received' => true,
        'place_visa_issued' => true,
        'apprentice' => true,
        'master_coe_type' => true,
    ];
}
