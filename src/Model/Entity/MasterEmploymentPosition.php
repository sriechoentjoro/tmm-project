<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterEmploymentPosition Entity
 *
 * @property int $id
 * @property string $title
 * @property float $cost_per_hour
 * @property \Cake\I18n\FrozenTime $updated
 * @property \Cake\I18n\FrozenTime $created
 */
class MasterEmploymentPosition extends Entity
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
        'title' => true,
        'cost_per_hour' => true,
        'updated' => true,
        'created' => true,
    ];
}
