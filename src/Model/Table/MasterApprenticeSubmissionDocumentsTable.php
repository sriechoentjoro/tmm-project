<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterApprenticeSubmissionDocuments Model
 *
 * @property \App\Model\Table\MasterApprenticeshipSubmissionDocumentCategoriesTable&\Cake\ORM\Association\BelongsTo $MasterApprenticeshipSubmissionDocumentCategories
 *
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MasterApprenticeSubmissionDocumentsTable extends Table
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

        $this->setTable('master_apprentice_submission_documents');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('MasterApprenticeSubmissionDocumentCategories', [
            'foreignKey' => 'master_apprenticeship_submission_document_category_id',
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
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('title_jp')
            ->maxLength('title_jp', 255)
            ->allowEmptyString('title_jp');

        $validator
            ->scalar('format')
            ->maxLength('format', 100)
            ->allowEmptyString('format');

        $validator
            ->boolean('is_translation_required')
            ->allowEmptyString('is_translation_required');

        $validator
            ->boolean('is_required')
            ->allowEmptyString('is_required');

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
        $rules->add($rules->existsIn(['master_apprenticeship_submission_document_category_id'], 'MasterApprenticeSubmissionDocumentCategories'));

        return $rules;
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
