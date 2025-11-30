<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterOccupations Model
 *
 * @property \App\Model\Table\OccupationCategoriesTable&\Cake\ORM\Association\BelongsTo $OccupationCategories
 *
 * @method \App\Model\Entity\MasterOccupation get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterOccupation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterOccupation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterOccupation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterOccupation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupation findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterOccupationsTable extends Table
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

        $this->setTable('master_occupations');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('MasterOccupationCategories', [
            'foreignKey' => 'occupation_category_id',
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
        $rules->add($rules->existsIn(['occupation_category_id'], 'MasterOccupationCategories'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_masters';
    }
}
