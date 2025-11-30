<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeCertification Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property string $title
 * @property string $institution_name
 * @property \Cake\I18n\FrozenDate|null $certification_date
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 */
class ApprenticeCertification extends Entity
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
        'title' => true,
        'institution_name' => true,
        'certification_date' => true,
        'detail' => true,
        'apprentice' => true,
    ];
}
