<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterApprenticeSubmissionDocumentCategories Model
 *
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocumentCategory findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MasterApprenticeSubmissionDocumentCategoriesTable extends Table
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

        $this->setTable('master_apprentice_submission_document_categories');
        $this->setDisplayField('name');
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
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

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
        return 'cms_tmm_apprentice_documents';
    }
}
