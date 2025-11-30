<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeRecordMedicalCheckUp Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property string $title
 * @property \Cake\I18n\FrozenDate $date_issued
 * @property int $master_medical_check_up_result_id
 * @property string|null $comment
 * @property string $clinic
 * @property string|null $mcu_files
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 * @property \App\Model\Entity\MasterMedicalCheckUpResult $master_medical_check_up_result
 */
class ApprenticeRecordMedicalCheckUp extends Entity
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
        'date_issued' => true,
        'master_medical_check_up_result_id' => true,
        'comment' => true,
        'clinic' => true,
        'mcu_files' => true,
        'apprentice' => true,
        'master_medical_check_up_result' => true,
    ];
}
