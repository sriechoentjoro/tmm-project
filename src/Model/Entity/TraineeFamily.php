<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeFamily Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property int $master_family_connection_id
 * @property string $name
 * @property int|null $age
 * @property int|null $master_occupation_id
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Trainee $trainee
 * @property \App\Model\Entity\MasterFamilyConnection $master_family_connection
 * @property \App\Model\Entity\MasterOccupation $master_occupation
 */
class TraineeFamily extends Entity
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
        'master_family_connection_id' => true,
        'name' => true,
        'age' => true,
        'master_occupation_id' => true,
        'detail' => true,
        'trainee' => true,
        'master_family_connection' => true,
        'master_occupation' => true,
    ];
}
