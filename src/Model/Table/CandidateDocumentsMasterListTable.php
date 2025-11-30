<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateDocumentsMasterList Model
 *
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 *
 * @method \App\Model\Entity\CandidateDocumentsMasterList get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentsMasterList findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateDocumentsMasterListTable extends Table
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

        $this->setTable('candidate_documents_master_list');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('CandidateDocumentCategories', [
            'foreignKey' => 'category_id',
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
            ->maxLength('title', 150)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->boolean('is_required')
            ->allowEmptyString('is_required');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

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
        $rules->add($rules->existsIn(['category_id'], 'CandidateDocumentCategories'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_lpk_candidate_documents';
    }
}
