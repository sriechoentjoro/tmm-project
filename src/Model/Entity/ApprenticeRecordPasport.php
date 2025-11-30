<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeRecordPasport Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property \Cake\I18n\FrozenDate $date_issue
 * @property string $place_issue
 * @property \Cake\I18n\FrozenDate $date_received
 * @property \Cake\I18n\FrozenDate $date_paid
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 */
class ApprenticeRecordPasport extends Entity
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
        'date_issue' => true,
        'place_issue' => true,
        'date_received' => true,
        'date_paid' => true,
        'apprentice' => true,
    ];
}
