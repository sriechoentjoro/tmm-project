<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeRecordMedicalCheckUps Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\MasterMedicalCheckUpResultsTable&\Cake\ORM\Association\BelongsTo $MasterMedicalCheckUpResults
 *
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeRecordMedicalCheckUpsTable extends Table
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

        $this->setTable('apprentice_record_medical_check_ups');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterMedicalCheckUpResults', [
            'foreignKey' => 'master_medical_check_up_result_id',
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
        $rules->add($rules->existsIn(['apprentice_id'], 'Apprentices'));
        $rules->add($rules->existsIn(['master_medical_check_up_result_id'], 'MasterMedicalCheckUpResults'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentice_documents';
    }
}
