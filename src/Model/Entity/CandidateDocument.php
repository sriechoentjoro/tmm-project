<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CandidateDocument Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property string $title
 * @property string $file_name
 * @property string $comments
 *
 * @property \App\Model\Entity\Candidate $candidate
 */
class CandidateDocument extends Entity
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
        'title' => true,
        'file_name' => true,
        'comments' => true,
        'candidate' => true,
    ];
}
