<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CandidateFamily Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $master_family_connection_id
 * @property string $name
 * @property int|null $age
 * @property int|null $master_occupation_id
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Candidate $candidate
 * @property \App\Model\Entity\MasterFamilyConnection $master_family_connection
 * @property \App\Model\Entity\MasterOccupation $master_occupation
 */
class CandidateFamily extends Entity
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
        'candidate_id' => true,
        'master_family_connection_id' => true,
        'name' => true,
        'age' => true,
        'master_occupation_id' => true,
        'detail' => true,
        'candidate' => true,
        'master_family_connection' => true,
        'master_occupation' => true,
    ];
}
