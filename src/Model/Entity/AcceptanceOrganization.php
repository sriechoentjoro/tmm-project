<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AcceptanceOrganization Entity
 *
 * @property int $id
 * @property string $title
 * @property int $master_japan_prefecture_id
 * @property int|null $post_code
 * @property string $address
 * @property string|null $director
 * @property string|null $director_hiragana
 *
 * @property \App\Model\Entity\MasterJapanPrefecture $master_japan_prefecture
 * @property \App\Model\Entity\AcceptanceOrganizationStory[] $acceptance_organization_stories
 */
class AcceptanceOrganization extends Entity
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
        'master_japan_prefecture_id' => true,
        'post_code' => true,
        'address' => true,
        'director' => true,
        'director_hiragana' => true,
        'master_japan_prefecture' => true,
        'acceptance_organization_stories' => true,
    ];
}
