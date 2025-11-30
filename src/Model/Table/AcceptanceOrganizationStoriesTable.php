<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AcceptanceOrganizationStories Model
 *
 * @property \App\Model\Table\AcceptanceOrganizationsTable&\Cake\ORM\Association\BelongsTo $AcceptanceOrganizations
 *
 * @method \App\Model\Entity\AcceptanceOrganizationStory get($primaryKey, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AcceptanceOrganizationStory findOrCreate($search, callable $callback = null, $options = [])
 */
class AcceptanceOrganizationStoriesTable extends Table
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

        $this->setTable('acceptance_organization_stories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcceptanceOrganizations', [
            'foreignKey' => 'acceptance_organization_id',
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
            ->date('date_occurrence')
            ->requirePresence('date_occurrence', 'create')
            ->notEmptyDate('date_occurrence');

        $validator
            ->scalar('problem_contents')
            ->requirePresence('problem_contents', 'create')
            ->notEmptyString('problem_contents');

        $validator
            ->scalar('problem_classification')
            ->maxLength('problem_classification', 256)
            ->requirePresence('problem_classification', 'create')
            ->notEmptyString('problem_classification');

        $validator
            ->scalar('problem_solution')
            ->maxLength('problem_solution', 256)
            ->requirePresence('problem_solution', 'create')
            ->notEmptyString('problem_solution');

        $validator
            ->scalar('problem_inference')
            ->maxLength('problem_inference', 256)
            ->requirePresence('problem_inference', 'create')
            ->notEmptyString('problem_inference');

        $validator
            ->scalar('image_path')
            ->maxLength('image_path', 256)
            ->allowEmptyFile('image_path');

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
        $rules->add($rules->existsIn(['acceptance_organization_id'], 'AcceptanceOrganizations'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_stakeholders';
    }
}
