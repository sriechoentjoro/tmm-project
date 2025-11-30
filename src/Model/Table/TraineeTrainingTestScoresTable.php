<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeTrainingTestScores Model
 *
 * @property \App\Model\Table\TraineesTable&\Cake\ORM\Association\BelongsTo $Trainees
 * @property \App\Model\Table\MasterTrainingCompetenciesTable&\Cake\ORM\Association\BelongsTo $MasterTrainingCompetencies
 * @property \App\Model\Table\MasterTrainingTestScoreGradesTable&\Cake\ORM\Association\BelongsTo $MasterTrainingTestScoreGrades
 *
 * @method \App\Model\Entity\TraineeTrainingTestScore get($primaryKey, $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingTestScore findOrCreate($search, callable $callback = null, $options = [])
 */
class TraineeTrainingTestScoresTable extends Table
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

        $this->setTable('trainee_training_test_scores');

        $this->belongsTo('Trainees', [
            'foreignKey' => 'trainee_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterTrainingCompetencies', [
            'foreignKey' => 'master_training_competency_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterTrainingTestScoreGrades', [
            'foreignKey' => 'master_training_test_score_grade_id',
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
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->date('test_date')
            ->requirePresence('test_date', 'create')
            ->notEmptyDate('test_date');

        $validator
            ->integer('score')
            ->requirePresence('score', 'create')
            ->notEmptyString('score');

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
        $rules->add($rules->existsIn(['master_training_competency_id'], 'MasterTrainingCompetencies'));
        $rules->add($rules->existsIn(['master_training_test_score_grade_id'], 'MasterTrainingTestScoreGrades'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainee_training_scorings';
    }
}
