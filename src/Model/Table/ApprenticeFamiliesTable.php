<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeFamilies Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\MasterFamilyConnectionsTable&\Cake\ORM\Association\BelongsTo $MasterFamilyConnections
 * @property \App\Model\Table\MasterOccupationsTable&\Cake\ORM\Association\BelongsTo $MasterOccupations
 *
 * @method \App\Model\Entity\ApprenticeFamily get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeFamily newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeFamily[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFamily|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeFamily saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeFamily patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFamily[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFamily findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeFamiliesTable extends Table
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

        $this->setTable('apprentice_families');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterFamilyConnections', [
            'foreignKey' => 'master_family_connection_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterOccupations', [
            'foreignKey' => 'master_occupation_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('age')
            ->allowEmptyString('age');

        $validator
            ->scalar('detail')
            ->allowEmptyString('detail');

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
        $rules->add($rules->existsIn(['master_family_connection_id'], 'MasterFamilyConnections'));
        $rules->add($rules->existsIn(['master_occupation_id'], 'MasterOccupations'));

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
