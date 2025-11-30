<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateRecordMedicalCheckUps Model
 *
 * @property \App\Model\Table\ApplicantsTable&\Cake\ORM\Association\BelongsTo $Applicants
 * @property \App\Model\Table\MedicalCheckUpResultsTable&\Cake\ORM\Association\BelongsTo $MedicalCheckUpResults
 *
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateRecordMedicalCheckUpsTable extends Table
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

        $this->setTable('candidate_record_medical_check_ups');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'applicant_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterMedicalCheckUpResults', [
            'foreignKey' => 'medical_check_up_result_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
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

        $validator
            ->date('date_issued')
            ->requirePresence('date_issued', 'create')
            ->notEmptyDate('date_issued');

        $validator
            ->scalar('comment')
            ->allowEmptyString('comment');

        $validator
            ->scalar('clinic')
            ->maxLength('clinic', 256)
            ->requirePresence('clinic', 'create')
            ->notEmptyString('clinic');

        $validator
            ->scalar('mcu_files')
            ->maxLength('mcu_files', 256)
            ->allowEmptyFile('mcu_files');

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
        $rules->add($rules->existsIn(['medical_check_up_result_id'], 'MasterMedicalCheckUpResults'));

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
