<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateFamilies Model
 *
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\BelongsTo $Candidates
 * @property \App\Model\Table\MasterFamilyConnectionsTable&\Cake\ORM\Association\BelongsTo $MasterFamilyConnections
 * @property \App\Model\Table\MasterOccupationsTable&\Cake\ORM\Association\BelongsTo $MasterOccupations
 *
 * @method \App\Model\Entity\CandidateFamily get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateFamily newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateFamily[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateFamily|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateFamily saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateFamily patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateFamily[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateFamily findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateFamiliesTable extends Table
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

        $this->setTable('candidate_families');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'candidate_id',
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
        $rules->add($rules->existsIn(['candidate_id'], 'Candidates'));
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
        return 'cms_lpk_candidates';
    }
}
