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
     * Recalculate an apprentice's medical standing from their check-ups.
     *
     * tmm-documentation runs the pre-departure check-up; tmm-training has to
     * hear the outcome before deciding that somebody leaves. Storing the
     * standing on the apprentice is what lets the departure screen show it
     * without opening every check-up, and it is the same rule the candidate
     * side already uses: any result an administrator has marked not fit makes
     * the standing 'fail'; otherwise a known fit result makes it 'pass'; a
     * check-up whose result nobody has classified leaves it unknown.
     *
     * @param int $apprenticeId Apprentice id.
     * @return string|null 'pass', 'fail', or null when nothing is known.
     */
    public function refreshMcuStanding($apprenticeId)
    {
        $apprenticeId = (int)$apprenticeId;
        if (!$apprenticeId || !$this->getSchema()->hasColumn('mcu_result')) {
            return null;
        }

        $standing = $this->mcuStandingFor($apprenticeId);

        $apprentice = $this->find()->where(['id' => $apprenticeId])->first();
        if (!$apprentice) {
            return null;
        }

        $apprentice->set('mcu_result', $standing);
        if ($this->getSchema()->hasColumn('mcu_checked_at')) {
            $apprentice->set('mcu_checked_at', $standing === null ? null : new \Cake\I18n\FrozenTime());
        }

        // Without validation or rules: these two fields are derived, not typed,
        // and an apprentice carrying some unrelated legacy problem would
        // otherwise make save() return false and lose the standing silently.
        if (!$this->save($apprentice, ['checkRules' => false, 'validate' => false])) {
            \Cake\Log\Log::error(sprintf(
                'refreshMcuStanding could not save apprentice %d: %s',
                $apprenticeId,
                json_encode($apprentice->getErrors())
            ));

            return null;
        }

        return $standing;
    }

    /**
     * The medical standing an apprentice's check-ups add up to.
     *
     * Two queries rather than a join: the check-ups live on
     * cms_tmm_apprentice_documents and the result types on cms_masters, and
     * CakePHP cannot join across connections.
     *
     * @param int $apprenticeId Apprentice id.
     * @return string|null 'pass', 'fail', or null when nothing is known.
     */
    public function mcuStandingFor($apprenticeId)
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        try {
            $resultIds = $locator->get('ApprenticeRecordMedicalCheckUps')->find()
                ->select(['master_medical_check_up_result_id'])
                ->where(['apprentice_id' => (int)$apprenticeId])
                ->enableHydration(false)
                ->extract('master_medical_check_up_result_id')
                ->toList();
        } catch (\Throwable $e) {
            return null;
        }

        $resultIds = array_filter(array_unique($resultIds));
        if (!$resultIds) {
            return null;
        }

        try {
            $results = $locator->get('MasterMedicalCheckUpResults');
            if (!$results->getSchema()->hasColumn('is_fit')) {
                return null;
            }
            $flags = $results->find()
                ->select(['id', 'is_fit'])
                ->where(['id IN' => $resultIds])
                ->enableHydration(false)
                ->combine('id', 'is_fit')
                ->toArray();
        } catch (\Throwable $e) {
            return null;
        }

        $known = false;
        foreach ($flags as $isFit) {
            if ($isFit === null || $isFit === '') {
                continue;
            }
            if ((int)$isFit === 0) {
                return 'fail';
            }
            $known = true;
        }

        return $known ? 'pass' : null;
    }

    /**
     * The three things tmm-training listens to before saying somebody leaves.
     *
     * Gathered for a whole list in four queries rather than four per row,
     * because the departure screen shows every apprentice at once.
     *
     *   certificate  the trainee certificate and the test average behind it,
     *                which is training's own evidence
     *   documents    how many required documents tmm-documentation has
     *                accepted, out of how many are required
     *   mcu          the standing from the pre-departure check-up
     *
     * Every lookup is wrapped: a missing table on an older installation
     * should leave a column blank, not take the screen down.
     *
     * @param array $apprentices Apprentice entities or arrays with id and trainee_id.
     * @return array Keyed by apprentice id.
     */
    public function departureEvidenceFor(array $apprentices)
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        $evidence = [];
        $traineeIds = [];
        $apprenticeIds = [];
        foreach ($apprentices as $apprentice) {
            $id = (int)(is_array($apprentice) ? $apprentice['id'] : $apprentice->id);
            $traineeId = (int)(is_array($apprentice)
                ? ($apprentice['trainee_id'] ?? 0)
                : ($apprentice->trainee_id ?? 0));
            $apprenticeIds[] = $id;
            if ($traineeId) {
                $traineeIds[$traineeId][] = $id;
            }
            $evidence[$id] = [
                'certificate' => null,
                'score_average' => null,
                'documents_accepted' => 0,
                'documents_required' => 0,
                'mcu' => null,
            ];
        }

        if (!$apprenticeIds) {
            return $evidence;
        }

        // --- training's own evidence: the certificate, and the average behind it
        if ($traineeIds) {
            try {
                $certificates = $locator->get('TraineeCertificates')->find()
                    ->select(['trainee_id', 'certificate_no'])
                    ->where(['trainee_id IN' => array_keys($traineeIds)])
                    ->enableHydration(false)
                    ->toArray();
                foreach ($certificates as $row) {
                    foreach ($traineeIds[$row['trainee_id']] ?? [] as $apprenticeId) {
                        $evidence[$apprenticeId]['certificate'] = $row['certificate_no'];
                    }
                }
            } catch (\Throwable $e) {
                // no certificate column on this installation - leave it blank
            }

            try {
                $scores = $locator->get('TraineeTrainingTestScores');
                $query = $scores->find();
                $rows = $query
                    ->select([
                        'trainee_id' => 'trainee_id',
                        'average' => $query->func()->avg('score'),
                    ])
                    ->where(['trainee_id IN' => array_keys($traineeIds)])
                    ->group(['trainee_id'])
                    ->enableHydration(false)
                    ->toArray();
                foreach ($rows as $row) {
                    foreach ($traineeIds[$row['trainee_id']] ?? [] as $apprenticeId) {
                        $evidence[$apprenticeId]['score_average'] = $row['average'] === null
                            ? null
                            : round((float)$row['average'], 1);
                    }
                }
            } catch (\Throwable $e) {
                // leave the average blank
            }
        }

        // --- documentation's evidence: required documents, and what is accepted
        $requiredIds = [];
        try {
            $required = $locator->get('MasterApprenticeSubmissionDocuments')->find()
                ->enableHydration(false)
                ->toArray();
            foreach ($required as $row) {
                // Only the documents marked required count towards readiness;
                // where no such column exists every document counts.
                if (array_key_exists('is_required', $row) && !$row['is_required']) {
                    continue;
                }
                $requiredIds[] = (int)$row['id'];
            }
        } catch (\Throwable $e) {
            $requiredIds = [];
        }

        $acceptedStatusIds = [];
        try {
            $statuses = $locator->get('MasterDocumentSubmissionStatuses')->find()
                ->enableHydration(false)
                ->toArray();
            foreach ($statuses as $row) {
                $title = strtolower((string)($row['name'] ?? $row['title'] ?? ''));
                if (strpos($title, 'accept') !== false
                    || strpos($title, 'approve') !== false
                    || strpos($title, 'complete') !== false
                    || strpos($title, 'verif') !== false
                    || strpos($title, 'terima') !== false
                    || strpos($title, 'lengkap') !== false
                ) {
                    $acceptedStatusIds[] = (int)$row['id'];
                }
            }
        } catch (\Throwable $e) {
            $acceptedStatusIds = [];
        }

        try {
            $documents = $locator->get('ApprenticeSubmissionDocuments')->find()
                ->select(['apprentice_id', 'apprenticeship_submission_document_id', 'master_document_submission_status_id'])
                ->where(['apprentice_id IN' => $apprenticeIds])
                ->enableHydration(false)
                ->toArray();
            $seen = [];
            foreach ($documents as $row) {
                $apprenticeId = (int)$row['apprentice_id'];
                $documentId = (int)$row['apprenticeship_submission_document_id'];
                if ($requiredIds && !in_array($documentId, $requiredIds, true)) {
                    continue;
                }
                // A document handed in twice is still one document.
                $key = $apprenticeId . ':' . $documentId;
                if (isset($seen[$key])) {
                    continue;
                }
                $statusId = (int)$row['master_document_submission_status_id'];
                // With no status list to go on, anything handed in counts.
                $accepted = $acceptedStatusIds
                    ? in_array($statusId, $acceptedStatusIds, true)
                    : true;
                if ($accepted) {
                    $seen[$key] = true;
                    $evidence[$apprenticeId]['documents_accepted']++;
                }
            }
        } catch (\Throwable $e) {
            // leave the counts at zero
        }

        foreach ($evidence as $id => $row) {
            $evidence[$id]['documents_required'] = count($requiredIds);
        }

        // --- the medical standing, already stored by refreshMcuStanding()
        if ($this->getSchema()->hasColumn('mcu_result')) {
            try {
                $standings = $this->find()
                    ->select(['id', 'mcu_result'])
                    ->where(['id IN' => $apprenticeIds])
                    ->enableHydration(false)
                    ->combine('id', 'mcu_result')
                    ->toArray();
                foreach ($standings as $id => $standing) {
                    $evidence[$id]['mcu'] = $standing;
                }
            } catch (\Throwable $e) {
                // leave it unknown
            }
        }

        return $evidence;
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
