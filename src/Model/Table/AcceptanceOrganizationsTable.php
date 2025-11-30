<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AcceptanceOrganizations Model
 *
 * @property \App\Model\Table\MasterJapanPrefecturesTable&\Cake\ORM\Association\BelongsTo $MasterJapanPrefectures
 * @property \App\Model\Table\AcceptanceOrganizationStoriesTable&\Cake\ORM\Association\HasMany $AcceptanceOrganizationStories
 *
 * @method \App\Model\Entity\AcceptanceOrganization get($primaryKey, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganization findOrCreate($search, callable $callback = null, $options = [])
 */
class AcceptanceOrganizationsTable extends Table
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

        $this->setTable('acceptance_organizations');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('MasterJapanPrefectures', [
            'foreignKey' => 'master_japan_prefecture_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('AcceptanceOrganizationStories', [
            'foreignKey' => 'acceptance_organization_id',
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
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('post_code')
            ->allowEmptyString('post_code');

        $validator
            ->scalar('address')
            ->maxLength('address', 256)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('director')
            ->maxLength('director', 256)
            ->allowEmptyString('director');

        $validator
            ->scalar('director_hiragana')
            ->maxLength('director_hiragana', 256)
            ->allowEmptyString('director_hiragana');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->requirePresence('status', 'create')
            ->notEmptyString('status')
            ->inList('status', ['active', 'suspended', 'inactive'], 'Invalid status value');

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
        $rules->add($rules->existsIn(['master_japan_prefecture_id'], 'MasterJapanPrefectures'));

        return $rules;
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
