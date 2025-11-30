<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CooperativeAssociations Model
 *
 * @property \App\Model\Table\CooperativeAssociationStoriesTable&\Cake\ORM\Association\HasMany $CooperativeAssociationStories
 *
 * @method \App\Model\Entity\CooperativeAssociation get($primaryKey, $options = [])
 * @method \App\Model\Entity\CooperativeAssociation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CooperativeAssociation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CooperativeAssociation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociation findOrCreate($search, callable $callback = null, $options = [])
 */
class CooperativeAssociationsTable extends Table
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

        $this->setTable('cooperative_associations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('CooperativeAssociationStories', [
            'foreignKey' => 'cooperative_association_id',
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
            ->scalar('name')
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('address')
            ->maxLength('address', 256)
            ->allowEmptyString('address');

        $validator
            ->scalar('telephone')
            ->maxLength('telephone', 256)
            ->allowEmptyString('telephone');

        $validator
            ->date('date_permitted')
            ->allowEmptyDate('date_permitted');

        $validator
            ->date('date_expired')
            ->allowEmptyDate('date_expired');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->requirePresence('status', 'create')
            ->notEmptyString('status')
            ->inList('status', ['active', 'suspended', 'inactive'], 'Invalid status value');

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
