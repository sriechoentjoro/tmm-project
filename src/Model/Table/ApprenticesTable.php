<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Apprentices Model
 *
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\BelongsTo $Candidates
 * @property \App\Model\Table\TraineesTable&\Cake\ORM\Association\BelongsTo $Trainees
 * @property \App\Model\Table\ApprenticeOrdersTable&\Cake\ORM\Association\BelongsTo $ApprenticeOrders
 * @property &\Cake\ORM\Association\BelongsTo $Trainings
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
 * @property &\Cake\ORM\Association\BelongsTo $MasterInterviewResults
 * @property &\Cake\ORM\Association\BelongsTo $MasterRejectedReasons
 * @property \App\Model\Table\ApprenticeCertificationsTable&\Cake\ORM\Association\HasMany $ApprenticeCertifications
 * @property \App\Model\Table\ApprenticeCoursesTable&\Cake\ORM\Association\HasMany $ApprenticeCourses
 * @property \App\Model\Table\ApprenticeEducationsTable&\Cake\ORM\Association\HasMany $ApprenticeEducations
 * @property \App\Model\Table\ApprenticeExperiencesTable&\Cake\ORM\Association\HasMany $ApprenticeExperiences
 * @property \App\Model\Table\ApprenticeFamiliesTable&\Cake\ORM\Association\HasMany $ApprenticeFamilies
 * @property \App\Model\Table\ApprenticeFamilyStoriesTable&\Cake\ORM\Association\HasMany $ApprenticeFamilyStories
 *
 * @method \App\Model\Entity\Apprentice get($primaryKey, $options = [])
 * @method \App\Model\Entity\Apprentice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Apprentice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Apprentice|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Apprentice saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Apprentice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Apprentice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Apprentice findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticesTable extends Table
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

        $this->setTable('apprentices');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'candidate_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
        ]);
        $this->belongsTo('Trainees', [
            'foreignKey' => 'trainee_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
        ]);
        $this->belongsTo('ApprenticeOrders', [
            'foreignKey' => 'apprentice_order_id',
            'strategy' => 'select',
        ]);
        // COMMENTED OUT - Table 'trainings' doesn't exist in any database
        // $this->belongsTo('Trainings', [
        //     'foreignKey' => 'training_id',
        // ]);
        $this->belongsTo('VocationalTrainingInstitutions', [
            'foreignKey' => 'vocational_training_institution_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
        ]);
        $this->belongsTo('AcceptanceOrganizations', [
            'foreignKey' => 'acceptance_organization_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterGenders', [
            'foreignKey' => 'master_gender_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterReligions', [
            'foreignKey' => 'master_religion_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterMarriageStatuses', [
            'foreignKey' => 'master_marriage_status_id',
            'joinType' => 'INNER',
            'strategy' => 'select',
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
            'foreignKey' => 'blood_type_id',
            'strategy' => 'select',
        ]);
        // COMMENTED OUT - Table 'master_interview_results' doesn't exist
        // $this->belongsTo('MasterInterviewResults', [
        //     'foreignKey' => 'master_interview_result_id',
        // ]);
        // COMMENTED OUT - Table 'master_rejected_reasons' might not exist
        // $this->belongsTo('MasterRejectedReasons', [
        //     'foreignKey' => 'master_rejected_reason_id',
        // ]);
        $this->hasMany('ApprenticeCertifications', [
            'foreignKey' => 'apprentice_id',
        ]);
        $this->hasMany('ApprenticeCourses', [
            'foreignKey' => 'apprentice_id',
        ]);
        $this->hasMany('ApprenticeEducations', [
            'foreignKey' => 'apprentice_id',
        ]);
        $this->hasMany('ApprenticeExperiences', [
            'foreignKey' => 'apprentice_id',
        ]);
        $this->hasMany('ApprenticeFamilies', [
            'foreignKey' => 'apprentice_id',
        ]);
        $this->hasMany('ApprenticeFamilyStories', [
            'foreignKey' => 'apprentice_id',
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
            ->scalar('applicant_code')
            ->maxLength('applicant_code', 10)
            ->allowEmptyString('applicant_code');

        $validator
            ->scalar('tmm_code')
            ->maxLength('tmm_code', 256)
            ->requirePresence('tmm_code', 'create')
            ->notEmptyString('tmm_code');

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
        $rules->add($rules->existsIn(['candidate_id'], 'Candidates'));
        $rules->add($rules->existsIn(['trainee_id'], 'Trainees'));
        $rules->add($rules->existsIn(['apprentice_order_id'], 'ApprenticeOrders'));
        // COMMENTED OUT - Table 'trainings' doesn't exist
        // $rules->add($rules->existsIn(['training_id'], 'Trainings'));
        $rules->add($rules->existsIn(['vocational_training_institution_id'], 'VocationalTrainingInstitutions'));
        $rules->add($rules->existsIn(['acceptance_organization_id'], 'AcceptanceOrganizations'));
        $rules->add($rules->existsIn(['master_gender_id'], 'MasterGenders'));
        $rules->add($rules->existsIn(['master_religion_id'], 'MasterReligions'));
        $rules->add($rules->existsIn(['master_marriage_status_id'], 'MasterMarriageStatuses'));
        $rules->add($rules->existsIn(['master_propinsi_id'], 'MasterPropinsis'));
        $rules->add($rules->existsIn(['master_kabupaten_id'], 'MasterKabupatens'));
        $rules->add($rules->existsIn(['master_kecamatan_id'], 'MasterKecamatans'));
        $rules->add($rules->existsIn(['master_kelurahan_id'], 'MasterKelurahans'));
        $rules->add($rules->existsIn(['blood_type_id'], 'MasterBloodTypes'));
        // COMMENTED OUT - Table 'master_interview_results' doesn't exist
        // $rules->add($rules->existsIn(['master_interview_result_id'], 'MasterInterviewResults'));
        // COMMENTED OUT - Table 'master_rejected_reasons' might not exist
        // $rules->add($rules->existsIn(['master_rejected_reason_id'], 'MasterRejectedReasons'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentices';
    }
}
