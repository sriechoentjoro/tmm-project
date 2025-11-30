<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VocationalTrainingInstitutionStory Entity
 *
 * @property int $id
 * @property int $vocational_training_institution_id
 * @property \Cake\I18n\FrozenDate $date_occurrence
 * @property string $problem_contents
 * @property string $problem_classification
 * @property string $problem_solution
 * @property string $problem_inference
 * @property string|null $image_path
 *
 * @property \App\Model\Entity\VocationalTrainingInstitution $vocational_training_institution
 */
class VocationalTrainingInstitutionStory extends Entity
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
        'vocational_training_institution_id' => true,
        'date_occurrence' => true,
        'problem_contents' => true,
        'problem_classification' => true,
        'problem_solution' => true,
        'problem_inference' => true,
        'image_path' => true,
        'vocational_training_institution' => true,
    ];
}
