<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateExperiences Model
 *
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\BelongsTo $Candidates
 * @property \App\Model\Table\MasterEmployeeStatusesTable&\Cake\ORM\Association\BelongsTo $MasterEmployeeStatuses
 *
 * @method \App\Model\Entity\CandidateExperience get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateExperience newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateExperience[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateExperience|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateExperience saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateExperience patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateExperience[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateExperience findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateExperiencesTable extends Table
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

        $this->setTable('candidate_experiences');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'candidate_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterEmployeeStatuses', [
            'foreignKey' => 'master_employee_status_id',
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
            ->scalar('employment_start_year')
            ->allowEmptyString('employment_start_year');

        $validator
            ->scalar('employment_end_year')
            ->allowEmptyString('employment_end_year');

        $validator
            ->scalar('company_name')
            ->maxLength('company_name', 256)
            ->allowEmptyString('company_name');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('employment_awards')
            ->allowEmptyString('employment_awards');

        $validator
            ->integer('last_salary_amount')
            ->allowEmptyString('last_salary_amount');

        $validator
            ->scalar('job_detail')
            ->allowEmptyString('job_detail');

        $validator
            ->scalar('termination_reasons')
            ->allowEmptyString('termination_reasons');

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
        $rules->add($rules->existsIn(['candidate_id'], 'Candidates'));
        $rules->add($rules->existsIn(['master_employee_status_id'], 'MasterEmployeeStatuses'));

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
