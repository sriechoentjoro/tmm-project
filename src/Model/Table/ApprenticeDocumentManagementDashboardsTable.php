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
}
