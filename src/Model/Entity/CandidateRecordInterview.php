<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CandidateRecordInterview Entity
 *
 * @property int $id
 * @property int $applicant_id
 * @property string $title
 * @property int $master_candidate_interview_type_id
 * @property \Cake\I18n\FrozenDate $date_interview
 * @property int|null $master_candidate_interview_result_id
 * @property \Cake\I18n\FrozenDate $date_interview_result
 * @property string|null $comments
 *
 * @property \App\Model\Entity\Applicant $applicant
 * @property \App\Model\Entity\MasterCandidateInterviewType $master_candidate_interview_type
 * @property \App\Model\Entity\MasterCandidateInterviewResult $master_candidate_interview_result
 */
class CandidateRecordInterview extends Entity
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
        'applicant_id' => true,
        'title' => true,
        'master_candidate_interview_type_id' => true,
        'date_interview' => true,
        'master_candidate_interview_result_id' => true,
        'date_interview_result' => true,
        'comments' => true,
        'applicant' => true,
        'master_candidate_interview_type' => true,
        'master_candidate_interview_result' => true,
    ];
}
