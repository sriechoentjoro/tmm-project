<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterFamilyConnection Entity
 *
 * @property int $id
 * @property string|null $family_category
 * @property string|null $title_eng
 * @property string $title
 * @property string|null $title_kanji
 * @property \Cake\I18n\FrozenTime $updated
 * @property \Cake\I18n\FrozenTime $created
 */
class MasterFamilyConnection extends Entity
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
        'family_category' => true,
        'title_eng' => true,
        'title' => true,
        'title_kanji' => true,
        'updated' => true,
        'created' => true,
    ];
}
