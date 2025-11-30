<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateDocumentCategories Model
 *
 * @method \App\Model\Entity\CandidateDocumentCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateDocumentCategoriesTable extends Table
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

        $this->setTable('candidate_document_categories');
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
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        return $validator;
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
