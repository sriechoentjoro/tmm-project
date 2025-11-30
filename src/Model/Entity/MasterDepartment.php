<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterDepartment Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property \Cake\I18n\FrozenTime $updated
 * @property \Cake\I18n\FrozenTime $created
 */
class MasterDepartment extends Entity
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
        'code' => true,
        'title' => true,
        'updated' => true,
        'created' => true,
    ];
}
