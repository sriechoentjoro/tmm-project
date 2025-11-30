<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterApprenticeDepartureDocuments Model
 *
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeDepartureDocument findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MasterApprenticeDepartureDocumentsTable extends Table
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

        $this->setTable('master_apprentice_departure_documents');
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('format')
            ->maxLength('format', 100)
            ->allowEmptyString('format');

        $validator
            ->boolean('is_required')
            ->allowEmptyString('is_required');

        return $validator;
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
