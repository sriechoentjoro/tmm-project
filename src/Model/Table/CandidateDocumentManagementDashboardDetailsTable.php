<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CandidateDocumentManagementDashboardDetails Model
 *
 * @property \App\Model\Table\DashboardsTable&\Cake\ORM\Association\BelongsTo $Dashboards
 *
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail get($primaryKey, $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail findOrCreate($search, callable $callback = null, $options = [])
 */
class CandidateDocumentManagementDashboardDetailsTable extends Table
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

        $this->setTable('candidate_document_management_dashboard_details');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CandidateDocumentManagementDashboards', [
            'foreignKey' => 'dashboard_id',
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
            ->scalar('document_type')
            ->maxLength('document_type', 255)
            ->allowEmptyString('document_type');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->allowEmptyString('status');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

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
        $rules->add($rules->existsIn(['dashboard_id'], 'CandidateDocumentManagementDashboards'));

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
