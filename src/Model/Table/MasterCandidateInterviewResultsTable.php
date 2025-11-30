<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterCandidateInterviewResults Model
 *
 * @property \App\Model\Table\CandidateRecordInterviewsTable&\Cake\ORM\Association\HasMany $CandidateRecordInterviews
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\HasMany $Candidates
 *
 * @method \App\Model\Entity\MasterCandidateInterviewResult get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCandidateInterviewResult findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterCandidateInterviewResultsTable extends Table
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

        $this->setTable('master_candidate_interview_results');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('CandidateRecordInterviews', [
            'foreignKey' => 'master_candidate_interview_result_id',
            'strategy' => 'select',
        ]);
        $this->hasMany('Candidates', [
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
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        return $validator;
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
