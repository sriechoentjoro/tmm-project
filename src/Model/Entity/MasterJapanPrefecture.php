<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterJapanPrefecture Entity
 *
 * @property int $id
 * @property string $area
 * @property string $title
 * @property string $prefecture_jpg
 * @property string $capital
 *
 * @property \App\Model\Entity\AcceptanceOrganization[] $acceptance_organizations
 */
class MasterJapanPrefecture extends Entity
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
        'area' => true,
        'title' => true,
        'prefecture_jpg' => true,
        'capital' => true,
        'acceptance_organizations' => true,
    ];
}
