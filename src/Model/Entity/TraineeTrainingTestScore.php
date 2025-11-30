<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeTrainingTestScore Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property int $master_training_competency_id
 * @property \Cake\I18n\FrozenDate $test_date
 * @property int $score
 * @property int $master_training_test_score_grade_id
 *
 * @property \App\Model\Entity\Trainee $trainee
 * @property \App\Model\Entity\MasterTrainingCompetency $master_training_competency
 * @property \App\Model\Entity\MasterTrainingTestScoreGrade $master_training_test_score_grade
 */
class TraineeTrainingTestScore extends Entity
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
        'id' => true,
        'trainee_id' => true,
        'master_training_competency_id' => true,
        'test_date' => true,
        'score' => true,
        'master_training_test_score_grade_id' => true,
        'trainee' => true,
        'master_training_competency' => true,
        'master_training_test_score_grade' => true,
    ];
}
