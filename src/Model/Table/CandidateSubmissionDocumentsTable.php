<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateSubmissionDocuments Model
 *
 * @property \App\Model\Table\ApplicantsTable&\Cake\ORM\Association\BelongsTo $Applicants
 * @property \App\Model\Table\DocumentsTable&\Cake\ORM\Association\BelongsTo $Documents
 *
 * @method \App\Model\Entity\CandidateSubmissionDocument get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateSubmissionDocument findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateSubmissionDocumentsTable extends Table
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

        $this->setTable('candidate_submission_documents');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'applicant_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('CandidateDocuments', [
            'foreignKey' => 'document_id',
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
            ->boolean('submitted')
            ->allowEmptyString('submitted');

        $validator
            ->date('submission_date')
            ->allowEmptyDate('submission_date');

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
        $rules->add($rules->existsIn(['applicant_id'], 'Candidates'));
        $rules->add($rules->existsIn(['document_id'], 'CandidateDocuments'));

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
