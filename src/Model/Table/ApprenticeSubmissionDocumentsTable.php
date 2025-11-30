<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeSubmissionDocuments Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\ApprenticeshipSubmissionDocumentsTable&\Cake\ORM\Association\BelongsTo $ApprenticeshipSubmissionDocuments
 * @property \App\Model\Table\MasterDocumentSubmissionStatusesTable&\Cake\ORM\Association\BelongsTo $MasterDocumentSubmissionStatuses
 *
 * @method \App\Model\Entity\ApprenticeSubmissionDocument get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeSubmissionDocument findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ApprenticeSubmissionDocumentsTable extends Table
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

        $this->setTable('apprentice_submission_documents');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterApprenticeSubmissionDocuments', [
            'foreignKey' => 'apprenticeship_submission_document_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterDocumentSubmissionStatuses', [
            'foreignKey' => 'master_document_submission_status_id',
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
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->requirePresence('file_path', 'create')
            ->notEmptyFile('file_path');

        $validator
            ->integer('uploaded_by')
            ->allowEmptyString('uploaded_by');

        $validator
            ->dateTime('uploaded_at')
            ->allowEmptyDateTime('uploaded_at');

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
        $rules->add($rules->existsIn(['apprentice_id'], 'Apprentices'));
        $rules->add($rules->existsIn(['apprenticeship_submission_document_id'], 'MasterApprenticeSubmissionDocuments'));
        $rules->add($rules->existsIn(['master_document_submission_status_id'], 'MasterDocumentSubmissionStatuses'));

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
