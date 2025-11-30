<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeCourses Model
 *
 * @property \App\Model\Table\TraineesTable&\Cake\ORM\Association\BelongsTo $Trainees
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable&\Cake\ORM\Association\BelongsTo $VocationalTrainingInstitutions *
 * @method \App\Model\Entity\TraineeCourse get($primaryKey, $options = [])
 * @method \App\Model\Entity\TraineeCourse newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TraineeCourse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TraineeCourse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeCourse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeCourse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeCourse[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeCourse findOrCreate($search, callable $callback = null, $options = [])
 */
class TraineeCoursesTable extends Table
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

        $this->setTable('trainee_courses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trainees', [
            'foreignKey' => 'trainee_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('VocationalTrainingInstitutions', [
            'foreignKey' => 'vocational_training_institution_id',
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
            ->integer('course_year')
            ->requirePresence('course_year', 'create')
            ->notEmptyString('course_year');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('detail')
            ->allowEmptyString('detail');

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
        $rules->add($rules->existsIn(['trainee_id'], 'Trainees'));
        $rules->add($rules->existsIn(['vocational_training_institution_id'], 'VocationalTrainingInstitutions'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainees';
    }
}
