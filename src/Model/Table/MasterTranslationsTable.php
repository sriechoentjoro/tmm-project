<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterTranslations Model
 *
 * @method \App\Model\Entity\MasterTranslation get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterTranslation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterTranslation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterTranslation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTranslation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTranslation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTranslation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTranslation findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterTranslationsTable extends Table
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

        $this->setTable('master_translations');
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
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('jpn')
            ->maxLength('jpn', 255)
            ->requirePresence('jpn', 'create')
            ->notEmptyString('jpn');

        $validator
            ->scalar('ind')
            ->maxLength('ind', 255)
            ->requirePresence('ind', 'create')
            ->notEmptyString('ind');

        $validator
            ->scalar('eng')
            ->maxLength('eng', 255)
            ->requirePresence('eng', 'create')
            ->notEmptyString('eng');

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
