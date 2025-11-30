<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeCourses Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable&\Cake\ORM\Association\BelongsTo $VocationalTrainingInstitutions *
 * @method \App\Model\Entity\ApprenticeCourse get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeCourse newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeCourse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCourse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeCourse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeCourse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCourse[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCourse findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeCoursesTable extends Table
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

        $this->setTable('apprentice_courses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
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
        $rules->add($rules->existsIn(['apprentice_id'], 'Apprentices'));
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
        return 'cms_tmm_apprentices';
    }
}
