<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterTrainingCompetency Entity
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 *
 * @property \App\Model\Entity\TraineeScoreAverage[] $trainee_score_averages
 * @property \App\Model\Entity\TraineeTrainingTestScore[] $trainee_training_test_scores
 */
class MasterTrainingCompetency extends Entity
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
        'title' => true,
        'description' => true,
        'trainee_score_averages' => true,
        'trainee_training_test_scores' => true,
    ];
}
