<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterCandidateInterviewResult Entity
 *
 * @property int $id
 * @property string $title
 *
 * @property \App\Model\Entity\CandidateRecordInterview[] $candidate_record_interviews
 * @property \App\Model\Entity\Candidate[] $candidates
 */
class MasterCandidateInterviewResult extends Entity
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
        'title' => true,
        'candidate_record_interviews' => true,
        'candidates' => true,
    ];
}
