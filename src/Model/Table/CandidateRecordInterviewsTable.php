<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateRecordInterviews Model
 *
 * @property \App\Model\Table\ApplicantsTable&\Cake\ORM\Association\BelongsTo $Applicants
 * @property \App\Model\Table\MasterCandidateInterviewTypesTable&\Cake\ORM\Association\BelongsTo $MasterCandidateInterviewTypes
 * @property \App\Model\Table\MasterCandidateInterviewResultsTable&\Cake\ORM\Association\BelongsTo $MasterCandidateInterviewResults
 *
 * @method \App\Model\Entity\CandidateRecordInterview get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordInterview findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateRecordInterviewsTable extends Table
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

        $this->setTable('candidate_record_interviews');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'applicant_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterCandidateInterviewTypes', [
            'foreignKey' => 'master_candidate_interview_type_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterCandidateInterviewResults', [
            'foreignKey' => 'master_candidate_interview_result_id',
            'strategy' => 'select',
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
            ->scalar('title')
            ->maxLength('title', 256)
            ->notEmptyString('title');

        $validator
            ->date('date_interview')
            ->requirePresence('date_interview', 'create')
            ->notEmptyDate('date_interview');

        $validator
            ->date('date_interview_result')
            ->requirePresence('date_interview_result', 'create')
            ->notEmptyDate('date_interview_result');

        $validator
            ->scalar('comments')
            ->maxLength('comments', 256)
            ->allowEmptyString('comments');

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
        $rules->add($rules->existsIn(['applicant_id'], 'Candidates'));
        $rules->add($rules->existsIn(['master_candidate_interview_type_id'], 'MasterCandidateInterviewTypes'));
        $rules->add($rules->existsIn(['master_candidate_interview_result_id'], 'MasterCandidateInterviewResults'));

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
