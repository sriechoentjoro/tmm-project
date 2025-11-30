<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CooperativeAssociation Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $telephone
 * @property \Cake\I18n\FrozenDate|null $date_permitted
 * @property \Cake\I18n\FrozenDate|null $date_expired
 *
 * @property \App\Model\Entity\CooperativeAssociationStory[] $cooperative_association_stories
 */
class CooperativeAssociation extends Entity
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
        'name' => true,
        'address' => true,
        'telephone' => true,
        'date_permitted' => true,
        'date_expired' => true,
        'cooperative_association_stories' => true,
    ];
}
