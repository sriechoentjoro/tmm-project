<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeOrders Model
 *
 * @property \App\Model\Table\CooperativeAssociationsTable&\Cake\ORM\Association\BelongsTo $CooperativeAssociations
 * @property \App\Model\Table\AcceptanceOrganizationsTable&\Cake\ORM\Association\BelongsTo $AcceptanceOrganizations
 * @property &\Cake\ORM\Association\BelongsTo $JobCategories
 *
 * @method \App\Model\Entity\ApprenticeOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeOrder|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeOrder saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeOrder findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeOrdersTable extends Table
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

        $this->setTable('apprentice_orders');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('CooperativeAssociations', [
            'foreignKey' => 'cooperative_association_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AcceptanceOrganizations', [
            'foreignKey' => 'acceptance_organization_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterJobCategories', [
            'foreignKey' => 'master_job_category_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        
        // HasMany associations
        $this->hasMany('Apprentices', [
            'foreignKey' => 'apprentice_order_id',
            'strategy' => 'select',
            'dependent' => false,
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
            ->scalar('reference_file')
            ->maxLength('reference_file', 256)
            ->allowEmptyFile('reference_file');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('male_trainee_number')
            ->allowEmptyString('male_trainee_number');

        $validator
            ->integer('female_trainee_number')
            ->allowEmptyString('female_trainee_number');

        $validator
            ->scalar('departure_year')
            ->requirePresence('departure_year', 'create')
            ->notEmptyString('departure_year');

        $validator
            ->scalar('departure_month')
            ->maxLength('departure_month', 100)
            ->requirePresence('departure_month', 'create')
            ->notEmptyString('departure_month');

        $validator
            ->boolean('is_practical_test_required')
            ->requirePresence('is_practical_test_required', 'create')
            ->notEmptyString('is_practical_test_required');

        $validator
            ->scalar('other_requirements')
            ->maxLength('other_requirements', 256)
            ->allowEmptyString('other_requirements');

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
        $rules->add($rules->existsIn(['cooperative_association_id'], 'CooperativeAssociations'));
        $rules->add($rules->existsIn(['acceptance_organization_id'], 'AcceptanceOrganizations'));
        $rules->add($rules->existsIn(['master_job_category_id'], 'MasterJobCategories'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainees';
    }
}
