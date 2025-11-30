<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterJapanPrefectures Model
 *
 * @property \App\Model\Table\AcceptanceOrganizationsTable&\Cake\ORM\Association\HasMany $AcceptanceOrganizations
 *
 * @method \App\Model\Entity\MasterJapanPrefecture get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterJapanPrefecture findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterJapanPrefecturesTable extends Table
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

        $this->setTable('master_japan_prefectures');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('AcceptanceOrganizations', [
            'foreignKey' => 'master_japan_prefecture_id',
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
            ->scalar('area')
            ->maxLength('area', 256)
            ->requirePresence('area', 'create')
            ->notEmptyString('area');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('prefecture_jpg')
            ->maxLength('prefecture_jpg', 256)
            ->requirePresence('prefecture_jpg', 'create')
            ->notEmptyString('prefecture_jpg');

        $validator
            ->scalar('capital')
            ->maxLength('capital', 256)
            ->requirePresence('capital', 'create')
            ->notEmptyString('capital');

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_stakeholders';
    }
}
