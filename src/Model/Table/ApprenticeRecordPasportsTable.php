<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeRecordPasports Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 *
 * @method \App\Model\Entity\ApprenticeRecordPasport get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeRecordPasport findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeRecordPasportsTable extends Table
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

        $this->setTable('apprentice_record_pasports');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
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
            ->date('date_issue')
            ->requirePresence('date_issue', 'create')
            ->notEmptyDate('date_issue');

        $validator
            ->scalar('place_issue')
            ->maxLength('place_issue', 256)
            ->requirePresence('place_issue', 'create')
            ->notEmptyString('place_issue');

        $validator
            ->date('date_received')
            ->requirePresence('date_received', 'create')
            ->notEmptyDate('date_received');

        $validator
            ->date('date_paid')
            ->requirePresence('date_paid', 'create')
            ->notEmptyDate('date_paid');

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
