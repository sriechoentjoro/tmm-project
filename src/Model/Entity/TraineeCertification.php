<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeCertification Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property string $title
 * @property string $institution_name
 * @property \Cake\I18n\FrozenDate|null $certification_date
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Trainee $trainee
 */
class TraineeCertification extends Entity
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
        'trainee_id' => true,
        'title' => true,
        'institution_name' => true,
        'certification_date' => true,
        'detail' => true,
        'trainee' => true,
    ];
}
