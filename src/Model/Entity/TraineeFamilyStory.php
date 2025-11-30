<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeFamilyStory Entity
 *
 * @property int $id
 * @property int $trainee_id
 * @property string $title
 * @property \Cake\I18n\FrozenDate $date_occurrence
 * @property string $problem_contents
 * @property string $problem_classification
 * @property string $problem_solution
 * @property string $problem_inference
 * @property string|null $image_path
 *
 * @property \App\Model\Entity\Trainee $trainee
 */
class TraineeFamilyStory extends Entity
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
        'title' => true,
        'date_occurrence' => true,
        'problem_contents' => true,
        'problem_classification' => true,
        'problem_solution' => true,
        'problem_inference' => true,
        'image_path' => true,
        'trainee' => true,
    ];
}
