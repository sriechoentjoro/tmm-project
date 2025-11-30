<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterRejectedReasons Model
 *
 * @method \App\Model\Entity\MasterRejectedReason get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterRejectedReason newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterRejectedReason[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterRejectedReason|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterRejectedReason saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterRejectedReason patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterRejectedReason[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterRejectedReason findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterRejectedReasonsTable extends Table
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

        $this->setTable('master_rejected_reasons');
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

        $validator
            ->scalar('title_jpg')
            ->maxLength('title_jpg', 256)
            ->allowEmptyString('title_jpg');

        $validator
            ->scalar('details')
            ->allowEmptyString('details');

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
