<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterFamilyConnections Model
 *
 * @method \App\Model\Entity\MasterFamilyConnection get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterFamilyConnection findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MasterFamilyConnectionsTable extends Table
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

        $this->setTable('master_family_connections');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('family_category')
            ->maxLength('family_category', 256)
            ->allowEmptyString('family_category');

        $validator
            ->scalar('title_eng')
            ->maxLength('title_eng', 256)
            ->allowEmptyString('title_eng');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('title_kanji')
            ->maxLength('title_kanji', 256)
            ->allowEmptyString('title_kanji');

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
