<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeDocumentManagementDashboards Model
 *
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\BelongsTo $Candidates
 *
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeDocumentManagementDashboardsTable extends Table
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

        $this->setTable('apprentice_document_management_dashboards');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Candidates', [
            'foreignKey' => 'candidate_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
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
            ->integer('total_documents')
            ->allowEmptyString('total_documents');

        $validator
            ->integer('total_ready')
            ->allowEmptyString('total_ready');

        $validator
            ->integer('total_pending')
            ->allowEmptyString('total_pending');

        $validator
            ->integer('total_missing')
            ->allowEmptyString('total_missing');

        $validator
            ->dateTime('last_updated')
            ->allowEmptyDateTime('last_updated');

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
        $rules->add($rules->existsIn(['candidate_id'], 'Candidates'));

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

    /**
     * The document standing this row claims to summarise, counted for real.
     *
     * The four totals on this table are typed into a form and never touched
     * again. They were right on the day somebody entered them and have been
     * drifting ever since - a document accepted this morning does not move
     * them, and nothing on the page said they were a snapshot rather than a
     * count. A stored figure that nothing recomputes is not a slow figure; it
     * is a wrong one that looks exactly like a right one.
     *
     * So the register is counted directly. Every document on the master list
     * is one of three things for a given person: submitted and marked
     * submitted, present on the register but not yet submitted, or not on the
     * register at all.
     *
     * The dashboard lives in the candidate document database and is keyed to a
     * candidate, despite its name, so these are candidate documents that are
     * counted - candidate_submission_documents.document_id names a row of
     * candidate_document_master_lists, which is what CandidateDocuments'
     * own screen joins them on.
     *
     * @param array|null $candidateIds Limit to these candidates, or null for all.
     * @return array candidate_id => ['documents','ready','pending','missing']
     */
    public function standingFor(array $candidateIds = null)
    {
        $connection = $this->getConnection();

        try {
            $total = (int)$connection->execute(
                'SELECT COUNT(*) FROM candidate_document_master_lists')->fetch()[0];
        } catch (\Exception $e) {
            // Without the master list there is no denominator, and a count of
            // nothing would read as a person with no documents outstanding.
            return [];
        }

        $where = '';
        $params = [];
        if ($candidateIds !== null) {
            $candidateIds = array_values(array_unique(array_map('intval', $candidateIds)));
            if (!$candidateIds) {
                return [];
            }
            $where = ' WHERE applicant_id IN (' . implode(',', array_fill(0, count($candidateIds), '?')) . ')';
            $params = $candidateIds;
        }

        try {
            $rows = $connection->execute(
                'SELECT applicant_id,
                        COUNT(*) AS on_register,
                        SUM(CASE WHEN submitted = 1 THEN 1 ELSE 0 END) AS ready
                 FROM candidate_submission_documents' . $where . '
                 GROUP BY applicant_id', $params)->fetchAll('assoc');
        } catch (\Exception $e) {
            return [];
        }

        $standing = [];
        foreach ($rows as $row) {
            $onRegister = (int)$row['on_register'];
            $ready = (int)$row['ready'];
            $standing[(int)$row['applicant_id']] = [
                'documents' => $total,
                'ready' => $ready,
                'pending' => max(0, $onRegister - $ready),
                // Never negative: a register carrying more rows than the master
                // list has entries means something else is wrong, and a
                // negative "missing" would hide it behind an impossible number.
                'missing' => max(0, $total - $onRegister),
            ];
        }

        // A candidate with no register rows at all is missing everything, which
        // is a fact worth showing rather than an absence to skip.
        if ($candidateIds !== null) {
            foreach ($candidateIds as $id) {
                if (!isset($standing[$id])) {
                    $standing[$id] = ['documents' => $total, 'ready' => 0,
                        'pending' => 0, 'missing' => $total];
                }
            }
        }

        return $standing;
    }

    /**
     * Does what this row says still match what the register holds?
     *
     * @param \Cake\Datasource\EntityInterface $row A dashboard row.
     * @param array $standing One entry from standingFor().
     * @return array The fields that disagree, as [field => [stored, real]].
     */
    public function disagreements($row, array $standing)
    {
        $pairs = [
            'total_documents' => 'documents',
            'total_ready' => 'ready',
            'total_pending' => 'pending',
            'total_missing' => 'missing',
        ];

        $out = [];
        foreach ($pairs as $stored => $real) {
            if ((int)$row->get($stored) !== (int)$standing[$real]) {
                $out[$stored] = [(int)$row->get($stored), (int)$standing[$real]];
            }
        }

        return $out;
    }

    /**
     * Write the counted figures onto a row, so the typed ones stop drifting.
     *
     * Saved without validation or rules: these are counts, not user input, and
     * an unrelated problem on a legacy row must not stop the figures being
     * brought in line.
     *
     * @param int $candidateId Candidate id.
     * @return array|null The standing written, or null when there is nothing to write.
     */
    public function refresh($candidateId)
    {
        $candidateId = (int)$candidateId;
        $standing = $this->standingFor([$candidateId]);
        if (!isset($standing[$candidateId])) {
            return null;
        }

        $row = $this->find()->where(['candidate_id' => $candidateId])->first();
        if (!$row) {
            return null;
        }

        $row->set('total_documents', $standing[$candidateId]['documents']);
        $row->set('total_ready', $standing[$candidateId]['ready']);
        $row->set('total_pending', $standing[$candidateId]['pending']);
        $row->set('total_missing', $standing[$candidateId]['missing']);
        $row->set('last_updated', new \Cake\I18n\FrozenTime());

        if (!$this->save($row, ['checkRules' => false, 'validate' => false])) {
            \Cake\Log\Log::error(sprintf('dashboard refresh could not save candidate %d: %s',
                $candidateId, json_encode($row->getErrors())));

            return null;
        }

        return $standing[$candidateId];
    }
}
