<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Candidates Model
 *
 * @property \App\Model\Table\ApprenticeOrdersTable&\Cake\ORM\Association\BelongsTo $ApprenticeOrders
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable&\Cake\ORM\Association\BelongsTo $VocationalTrainingInstitutions
 * @property \App\Model\Table\AcceptanceOrganizationsTable&\Cake\ORM\Association\BelongsTo $AcceptanceOrganizations
 * @property \App\Model\Table\MasterGendersTable&\Cake\ORM\Association\BelongsTo $MasterGenders
 * @property \App\Model\Table\MasterReligionsTable&\Cake\ORM\Association\BelongsTo $MasterReligions
 * @property \App\Model\Table\MasterMarriageStatusesTable&\Cake\ORM\Association\BelongsTo $MasterMarriageStatuses
 * @property \App\Model\Table\MasterPropinsisTable&\Cake\ORM\Association\BelongsTo $MasterPropinsis
 * @property \App\Model\Table\MasterKabupatensTable&\Cake\ORM\Association\BelongsTo $MasterKabupatens
 * @property \App\Model\Table\MasterKecamatansTable&\Cake\ORM\Association\BelongsTo $MasterKecamatans
 * @property \App\Model\Table\MasterKelurahansTable&\Cake\ORM\Association\BelongsTo $MasterKelurahans
 * @property \App\Model\Table\MasterBloodTypesTable&\Cake\ORM\Association\BelongsTo $MasterBloodTypes
 * @property \App\Model\Table\MasterCandidateInterviewResultsTable&\Cake\ORM\Association\BelongsTo $MasterCandidateInterviewResults
 * @property \App\Model\Table\MasterRejectedReasonsTable&\Cake\ORM\Association\BelongsTo $MasterRejectedReasons
 * @property \App\Model\Table\CandidateCertificationsTable&\Cake\ORM\Association\HasMany $CandidateCertifications
 * @property \App\Model\Table\CandidateCoursesTable&\Cake\ORM\Association\HasMany $CandidateCourses
 * @property \App\Model\Table\CandidateDocumentsTable&\Cake\ORM\Association\HasMany $CandidateDocuments
 * @property \App\Model\Table\CandidateEducationsTable&\Cake\ORM\Association\HasMany $CandidateEducations
 * @property \App\Model\Table\CandidateExperiencesTable&\Cake\ORM\Association\HasMany $CandidateExperiences
 * @property \App\Model\Table\CandidateFamiliesTable&\Cake\ORM\Association\HasMany $CandidateFamilies
 *
 * @method \App\Model\Entity\Candidate get($primaryKey, $options = [])
 * @method \App\Model\Entity\Candidate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Candidate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Candidate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Candidate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Candidate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidatesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('candidates');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('ApprenticeOrders', [
            'foreignKey' => 'apprentice_order_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('VocationalTrainingInstitutions', [
            'foreignKey' => 'vocational_training_institution_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AcceptanceOrganizations', [
            'foreignKey' => 'acceptance_organization_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterGenders', [
            'foreignKey' => 'master_gender_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterReligions', [
            'foreignKey' => 'master_religion_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterMarriageStatuses', [
            'foreignKey' => 'master_marriage_status_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterPropinsis', [
            'foreignKey' => 'master_propinsi_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterKabupatens', [
            'foreignKey' => 'master_kabupaten_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterKecamatans', [
            'foreignKey' => 'master_kecamatan_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterKelurahans', [
            'foreignKey' => 'master_kelurahan_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterBloodTypes', [
            'foreignKey' => 'master_blood_type_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterCandidateInterviewResults', [
            'foreignKey' => 'master_candidate_interview_result_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterRejectedReasons', [
            'foreignKey' => 'master_rejected_reason_id',
            'strategy' => 'select',
        ]);
        $this->hasMany('CandidateCertifications', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateCourses', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateDocuments', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateEducations', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateExperiences', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateFamilies', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('Interviews', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('McuRecords', [
            'foreignKey' => 'candidate_id',
        ]);
        $this->hasMany('CandidateStatuses', [
            'foreignKey' => 'candidate_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('candidate_code')
            ->maxLength('candidate_code', 10)
            ->allowEmptyString('candidate_code');

        $validator
            ->scalar('identity_number')
            ->maxLength('identity_number', 16)
            ->requirePresence('identity_number', 'create')
            ->notEmptyString('identity_number');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('name_katakana')
            ->maxLength('name_katakana', 256)
            ->allowEmptyString('name_katakana');

        $validator
            ->scalar('birth_place')
            ->maxLength('birth_place', 100)
            ->requirePresence('birth_place', 'create')
            ->notEmptyString('birth_place');

        $validator
            ->scalar('birth_place_katakana')
            ->maxLength('birth_place_katakana', 256)
            ->allowEmptyString('birth_place_katakana');

        $validator
            ->date('birth_date')
            ->requirePresence('birth_date', 'create')
            ->notEmptyDate('birth_date');

        $validator
            ->scalar('telephone_mobile')
            ->maxLength('telephone_mobile', 12)
            ->allowEmptyString('telephone_mobile');

        $validator
            ->scalar('telephone_emergency')
            ->maxLength('telephone_emergency', 256)
            ->requirePresence('telephone_emergency', 'create')
            ->notEmptyString('telephone_emergency');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->integer('post_code')
            ->allowEmptyString('post_code');

        $validator
            ->scalar('address')
            ->maxLength('address', 256)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('image_photo')
            ->maxLength('image_photo', 256)
            ->allowEmptyFile('image_photo');

        $validator
            ->scalar('strengths')
            ->maxLength('strengths', 156)
            ->allowEmptyString('strengths');

        $validator
            ->scalar('weaknesses')
            ->maxLength('weaknesses', 256)
            ->allowEmptyString('weaknesses');

        $validator
            ->scalar('hobby')
            ->maxLength('hobby', 256)
            ->allowEmptyString('hobby');

        $validator
            ->integer('last_salary_amount')
            ->allowEmptyString('last_salary_amount');

        $validator
            ->scalar('application_reasons')
            ->maxLength('application_reasons', 256)
            ->allowEmptyString('application_reasons');

        $validator
            ->boolean('is_ever_went_to_japan')
            ->allowEmptyString('is_ever_went_to_japan');

        $validator
            ->boolean('will_go_to_japan_after_finished')
            ->allowEmptyString('will_go_to_japan_after_finished');

        $validator
            ->scalar('expected_work_upon_returning_to_japan')
            ->maxLength('expected_work_upon_returning_to_japan', 256)
            ->allowEmptyString('expected_work_upon_returning_to_japan');

        $validator
            ->boolean('is_holding_passport')
            ->allowEmptyString('is_holding_passport');

        $validator
            ->integer('saving_goal_amount')
            ->allowEmptyString('saving_goal_amount');

        $validator
            ->decimal('body_weight')
            ->allowEmptyString('body_weight');

        $validator
            ->decimal('body_height')
            ->allowEmptyString('body_height');

        $validator
            ->boolean('is_wear_eye_glasses')
            ->allowEmptyString('is_wear_eye_glasses');

        $validator
            ->scalar('explain_eye_condition')
            ->maxLength('explain_eye_condition', 256)
            ->allowEmptyString('explain_eye_condition');

        $validator
            ->boolean('is_color_blind')
            ->allowEmptyString('is_color_blind');

        $validator
            ->scalar('explain_color_blind')
            ->maxLength('explain_color_blind', 256)
            ->allowEmptyString('explain_color_blind');

        $validator
            ->boolean('is_right_handed')
            ->allowEmptyString('is_right_handed');

        $validator
            ->boolean('is_smoking')
            ->allowEmptyString('is_smoking');

        $validator
            ->boolean('is_drinking_alcohol')
            ->allowEmptyString('is_drinking_alcohol');

        $validator
            ->boolean('is_tattooed')
            ->allowEmptyString('is_tattooed');

        $validator
            ->scalar('link_whatsapp')
            ->maxLength('link_whatsapp', 256)
            ->allowEmptyString('link_whatsapp');

        $validator
            ->scalar('link_line')
            ->maxLength('link_line', 256)
            ->allowEmptyString('link_line');

        $validator
            ->scalar('link_instagram')
            ->maxLength('link_instagram', 256)
            ->allowEmptyString('link_instagram');

        $validator
            ->scalar('link_facebook')
            ->maxLength('link_facebook', 256)
            ->allowEmptyString('link_facebook');

        $validator
            ->scalar('link_tiktok')
            ->maxLength('link_tiktok', 256)
            ->allowEmptyString('link_tiktok');

        $validator
            ->boolean('is_training_pass')
            ->allowEmptyString('is_training_pass');

        $validator
            ->boolean('is_apprenticeship_pass')
            ->allowEmptyString('is_apprenticeship_pass');

        $validator
            ->scalar('specific_rejected_reason')
            ->allowEmptyString('specific_rejected_reason');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['apprentice_order_id'], 'ApprenticeOrders'));
        $rules->add($rules->existsIn(['vocational_training_institution_id'], 'VocationalTrainingInstitutions'));
        $rules->add($rules->existsIn(['acceptance_organization_id'], 'AcceptanceOrganizations'));
        $rules->add($rules->existsIn(['master_gender_id'], 'MasterGenders'));
        $rules->add($rules->existsIn(['master_religion_id'], 'MasterReligions'));
        $rules->add($rules->existsIn(['master_marriage_status_id'], 'MasterMarriageStatuses'));
        $rules->add($rules->existsIn(['master_propinsi_id'], 'MasterPropinsis'));
        $rules->add($rules->existsIn(['master_kabupaten_id'], 'MasterKabupatens'));
        $rules->add($rules->existsIn(['master_kecamatan_id'], 'MasterKecamatans'));
        $rules->add($rules->existsIn(['master_kelurahan_id'], 'MasterKelurahans'));
        $rules->add($rules->existsIn(['master_blood_type_id'], 'MasterBloodTypes'));
        $rules->add($rules->existsIn(['master_candidate_interview_result_id'], 'MasterCandidateInterviewResults'));
        $rules->add($rules->existsIn(['master_rejected_reason_id'], 'MasterRejectedReasons'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_lpk_candidates';
    }
}
