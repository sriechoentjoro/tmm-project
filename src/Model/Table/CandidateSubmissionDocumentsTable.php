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
        // document_id names a row of candidate_document_master_lists - the list
        // of document TYPES a candidate has to hand in - not a row of
        // candidate_documents, which is a file one candidate uploaded.
        //
        // The association used to point at the latter, and so did the rule
        // below and the dropdown on the form, while the form's own help text
        // said "From the master list of candidate documents" and
        // CandidateDocumentsController::index() read these rows as master-list
        // ids to build its checklist. The two tables number their rows
        // independently and both start at 1, so a wrong id always found a row
        // and nothing ever errored: a document handed in was simply recorded
        // against the wrong type, and the checklist showed it as still
        // outstanding.
        //
        // Aliased MasterDocuments rather than CandidateDocuments: a real table
        // already carries that name, and calling this one by it is how the
        // confusion started.
        $this->belongsTo('MasterDocuments', [
            'className' => 'CandidateDocumentsMasterList',
            'foreignKey' => 'document_id',
            // Kept as $row->document, which is what the column is called and
            // what the entity already declares accessible.
            'propertyName' => 'document',
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
        $rules->add($rules->existsIn(['document_id'], 'MasterDocuments'));

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
