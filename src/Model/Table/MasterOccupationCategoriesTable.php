<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterOccupationCategories Model
 *
 * @method \App\Model\Entity\MasterOccupationCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterOccupationCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterOccupationCategoriesTable extends Table
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

        $this->setTable('master_occupation_categories');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
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
        return 'cms_masters';
    }
}
