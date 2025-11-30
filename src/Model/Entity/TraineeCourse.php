<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeCourse Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property int $vocational_training_institution_id
 * @property int $course_major_id
 * @property int $course_year
 * @property string $title
 * @property string|null $detail
 *
 * @property \App\Model\Entity\Trainee $trainee
 * @property \App\Model\Entity\VocationalTrainingInstitution $vocational_training_institution
 * @property \App\Model\Entity\CourseMajor $course_major
 */
class TraineeCourse extends Entity
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
        'vocational_training_institution_id' => true,
        'course_major_id' => true,
        'course_year' => true,
        'title' => true,
        'detail' => true,
        'trainee' => true,
        'vocational_training_institution' => true,
        'course_major' => true,
    ];
}
