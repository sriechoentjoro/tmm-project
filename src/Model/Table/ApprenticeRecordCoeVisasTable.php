<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeRecordCoeVisas Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\MasterCoeTypesTable&\Cake\ORM\Association\BelongsTo $MasterCoeTypes
 *
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeRecordCoeVisasTable extends Table
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

        $this->setTable('apprentice_record_coe_visas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterApprenticeCoeTypes', [
            'foreignKey' => 'master_coe_type_id',
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
            ->date('date_coe_received')
            ->requirePresence('date_coe_received', 'create')
            ->notEmptyDate('date_coe_received');

        $validator
            ->date('date_visa_application')
            ->requirePresence('date_visa_application', 'create')
            ->notEmptyDate('date_visa_application');

        $validator
            ->date('date_visa_received')
            ->requirePresence('date_visa_received', 'create')
            ->notEmptyDate('date_visa_received');

        $validator
            ->scalar('place_visa_issued')
            ->maxLength('place_visa_issued', 256)
            ->requirePresence('place_visa_issued', 'create')
            ->notEmptyString('place_visa_issued');

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
        $rules->add($rules->existsIn(['master_coe_type_id'], 'MasterApprenticeCoeTypes'));

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
