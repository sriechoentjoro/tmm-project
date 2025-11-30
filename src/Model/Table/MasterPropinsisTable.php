<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterPropinsis Model
 *
 * @method \App\Model\Entity\MasterPropinsi get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterPropinsi newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterPropinsi[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterPropinsi|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterPropinsi saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterPropinsi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterPropinsi[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterPropinsi findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterPropinsisTable extends Table
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

        $this->setTable('master_propinsis');
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
            ->scalar('kode_propinsi')
            ->maxLength('kode_propinsi', 11)
            ->requirePresence('kode_propinsi', 'create')
            ->notEmptyString('kode_propinsi');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
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
