<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeTrainingBatch Entity
 *
 * @property int $id
 * @property string|null $batch_name
 * @property \Cake\I18n\FrozenDate|null $departure_plan_date
 * @property bool $is_training_location_moved
 * @property int $training_term_of_months
 * @property string $origin_training_location
 * @property string|null $moved_training_location
 * @property \Cake\I18n\FrozenDate $origin_start_plan_date
 * @property \Cake\I18n\FrozenDate $origin_finish_plan_date
 * @property \Cake\I18n\FrozenDate|null $moved_start_plan_date
 * @property \Cake\I18n\FrozenDate|null $moved_finished_plan_date
 */
class TraineeTrainingBatch extends Entity
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
        'batch_name' => true,
        'departure_plan_date' => true,
        'is_training_location_moved' => true,
        'training_term_of_months' => true,
        'origin_training_location' => true,
        'moved_training_location' => true,
        'origin_start_plan_date' => true,
        'origin_finish_plan_date' => true,
        'moved_start_plan_date' => true,
        'moved_finished_plan_date' => true,
    ];
}
