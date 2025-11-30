<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterMarriageStatus Entity
 *
 * @property int $id
 * @property string|null $slug
 * @property string $title
 * @property string|null $title_jpg
 * @property \Cake\I18n\FrozenTime $updated
 * @property \Cake\I18n\FrozenTime $created
 */
class MasterMarriageStatus extends Entity
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
        'slug' => true,
        'title' => true,
        'title_jpg' => true,
        'updated' => true,
        'created' => true,
    ];
}
