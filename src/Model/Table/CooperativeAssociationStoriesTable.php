<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CooperativeAssociationStories Model
 *
 * @property \App\Model\Table\CooperativeAssociationsTable&\Cake\ORM\Association\BelongsTo $CooperativeAssociations
 *
 * @method \App\Model\Entity\CooperativeAssociationStory get($primaryKey, $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CooperativeAssociationStory findOrCreate($search, callable $callback = null, $options = [])
 */
class CooperativeAssociationStoriesTable extends Table
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

        $this->setTable('cooperative_association_stories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CooperativeAssociations', [
            'foreignKey' => 'cooperative_association_id',
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
        $rules->add($rules->existsIn(['cooperative_association_id'], 'CooperativeAssociations'));

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
