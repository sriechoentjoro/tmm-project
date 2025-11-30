<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Candidate Entity
 *
 * @property int $id
 * @property int|null $apprentice_order_id
 * @property string|null $candidate_code
 * @property int $vocational_training_institution_id
 * @property int|null $acceptance_organization_id
 * @property string $identity_number
 * @property string $name
 * @property string|null $name_katakana
 * @property int $master_gender_id
 * @property int $master_religion_id
 * @property int $master_marriage_status_id
 * @property string $birth_place
 * @property string|null $birth_place_katakana
 * @property \Cake\I18n\FrozenDate $birth_date
 * @property string|null $telephone_mobile
 * @property string $telephone_emergency
 * @property string|null $email
 * @property int|null $master_propinsi_id
 * @property int|null $master_kabupaten_id
 * @property int|null $master_kecamatan_id
 * @property int|null $master_kelurahan_id
 * @property int|null $post_code
 * @property string $address
 * @property string|null $image_photo
 * @property string|null $strengths
 * @property string|null $weaknesses
 * @property string|null $hobby
 * @property int|null $last_salary_amount
 * @property string|null $application_reasons
 * @property bool|null $is_ever_went_to_japan
 * @property bool|null $will_go_to_japan_after_finished
 * @property string|null $expected_work_upon_returning_to_japan
 * @property bool|null $is_holding_passport
 * @property int|null $saving_goal_amount
 * @property int|null $master_blood_type_id
 * @property float|null $body_weight
 * @property float|null $body_height
 * @property bool|null $is_wear_eye_glasses
 * @property string|null $explain_eye_condition
 * @property bool|null $is_color_blind
 * @property string|null $explain_color_blind
 * @property bool|null $is_right_handed
 * @property bool|null $is_smoking
 * @property bool|null $is_drinking_alcohol
 * @property bool|null $is_tattooed
 * @property string|null $link_whatsapp
 * @property string|null $link_line
 * @property string|null $link_instagram
 * @property string|null $link_facebook
 * @property string|null $link_tiktok
 * @property int|null $master_candidate_interview_result_id
 * @property bool|null $is_training_pass
 * @property bool|null $is_apprenticeship_pass
 * @property int|null $master_rejected_reason_id
 * @property string|null $specific_rejected_reason
 *
 * @property \App\Model\Entity\ApprenticeOrder $apprentice_order
 * @property \App\Model\Entity\VocationalTrainingInstitution $vocational_training_institution
 * @property \App\Model\Entity\AcceptanceOrganization $acceptance_organization
 * @property \App\Model\Entity\MasterGender $master_gender
 * @property \App\Model\Entity\MasterReligion $master_religion
 * @property \App\Model\Entity\MasterMarriageStatus $master_marriage_status
 * @property \App\Model\Entity\MasterPropinsi $master_propinsi
 * @property \App\Model\Entity\MasterKabupaten $master_kabupaten
 * @property \App\Model\Entity\MasterKecamatan $master_kecamatan
 * @property \App\Model\Entity\MasterKelurahan $master_kelurahan
 * @property \App\Model\Entity\MasterBloodType $master_blood_type
 * @property \App\Model\Entity\MasterCandidateInterviewResult $master_candidate_interview_result
 * @property \App\Model\Entity\MasterRejectedReason $master_rejected_reason
 * @property \App\Model\Entity\CandidateCertification[] $candidate_certifications
 * @property \App\Model\Entity\CandidateCourse[] $candidate_courses
 * @property \App\Model\Entity\CandidateDocument[] $candidate_documents
 * @property \App\Model\Entity\CandidateEducation[] $candidate_educations
 * @property \App\Model\Entity\CandidateExperience[] $candidate_experiences
 * @property \App\Model\Entity\CandidateFamily[] $candidate_families
 */
class Candidate extends Entity
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
        'apprentice_order_id' => true,
        'candidate_code' => true,
        'vocational_training_institution_id' => true,
        'acceptance_organization_id' => true,
        'identity_number' => true,
        'name' => true,
        'name_katakana' => true,
        'master_gender_id' => true,
        'master_religion_id' => true,
        'master_marriage_status_id' => true,
        'birth_place' => true,
        'birth_place_katakana' => true,
        'birth_date' => true,
        'telephone_mobile' => true,
        'telephone_emergency' => true,
        'email' => true,
        'master_propinsi_id' => true,
        'master_kabupaten_id' => true,
        'master_kecamatan_id' => true,
        'master_kelurahan_id' => true,
        'post_code' => true,
        'address' => true,
        'image_photo' => true,
        'strengths' => true,
        'weaknesses' => true,
        'hobby' => true,
        'last_salary_amount' => true,
        'application_reasons' => true,
        'is_ever_went_to_japan' => true,
        'will_go_to_japan_after_finished' => true,
        'expected_work_upon_returning_to_japan' => true,
        'is_holding_passport' => true,
        'saving_goal_amount' => true,
        'master_blood_type_id' => true,
        'body_weight' => true,
        'body_height' => true,
        'is_wear_eye_glasses' => true,
        'explain_eye_condition' => true,
        'is_color_blind' => true,
        'explain_color_blind' => true,
        'is_right_handed' => true,
        'is_smoking' => true,
        'is_drinking_alcohol' => true,
        'is_tattooed' => true,
        'link_whatsapp' => true,
        'link_line' => true,
        'link_instagram' => true,
        'link_facebook' => true,
        'link_tiktok' => true,
        'master_candidate_interview_result_id' => true,
        'is_training_pass' => true,
        'is_apprenticeship_pass' => true,
        'master_rejected_reason_id' => true,
        'specific_rejected_reason' => true,
        'apprentice_order' => true,
        'vocational_training_institution' => true,
        'acceptance_organization' => true,
        'master_gender' => true,
        'master_religion' => true,
        'master_marriage_status' => true,
        'master_propinsi' => true,
        'master_kabupaten' => true,
        'master_kecamatan' => true,
        'master_kelurahan' => true,
        'master_blood_type' => true,
        'master_candidate_interview_result' => true,
        'master_rejected_reason' => true,
        'candidate_certifications' => true,
        'candidate_courses' => true,
        'candidate_documents' => true,
        'candidate_educations' => true,
        'candidate_experiences' => true,
        'candidate_families' => true,
    ];
}
