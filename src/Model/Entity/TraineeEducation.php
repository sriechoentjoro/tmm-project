<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeEducation Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property int $master_strata_id
 * @property int|null $master_propinsi_id
 * @property int|null $master_kabupaten_id
 * @property \Cake\I18n\FrozenDate|null $college_entry_date
 * @property \Cake\I18n\FrozenDate|null $college_graduate_date
 * @property string $college_name
 * @property string|null $college_major
 *
 * @property \App\Model\Entity\Trainee $trainee
 * @property \App\Model\Entity\MasterStrata $master_strata
 * @property \App\Model\Entity\MasterPropinsi $master_propinsi
 * @property \App\Model\Entity\MasterKabupaten $master_kabupaten
 */
class TraineeEducation extends Entity
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
        'master_strata_id' => true,
        'master_propinsi_id' => true,
        'master_kabupaten_id' => true,
        'college_entry_date' => true,
        'college_graduate_date' => true,
        'college_name' => true,
        'college_major' => true,
        'trainee' => true,
        'master_strata' => true,
        'master_propinsi' => true,
        'master_kabupaten' => true,
    ];
}
