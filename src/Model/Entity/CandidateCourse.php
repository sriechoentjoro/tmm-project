<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CandidateCourse Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $vocational_training_institution_id
 * @property int $master_course_major_id
 * @property int $course_year
 * @property string $title
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Candidate $candidate
 * @property \App\Model\Entity\VocationalTrainingInstitution $vocational_training_institution
 * @property \App\Model\Entity\MasterCourseMajor $master_course_major
 */
class CandidateCourse extends Entity
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
        'vocational_training_institution_id' => true,
        'master_course_major_id' => true,
        'course_year' => true,
        'title' => true,
        'detail' => true,
        'candidate' => true,
        'vocational_training_institution' => true,
        'master_course_major' => true,
    ];
}
