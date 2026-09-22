<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeScoreAverages Model
 *
 * @property \App\Model\Table\TraineesTable&\Cake\ORM\Association\BelongsTo $Trainees
 * @property \App\Model\Table\MasterTrainingCompetenciesTable&\Cake\ORM\Association\BelongsTo $MasterTrainingCompetencies
 * @property \App\Model\Table\MasterTrainingTestScoreGradesTable&\Cake\ORM\Association\BelongsTo $MasterTrainingTestScoreGrades
 *
 * @method \App\Model\Entity\TraineeScoreAverage get($primaryKey, $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeScoreAverage findOrCreate($search, callable $callback = null, $options = [])
 */
class TraineeScoreAveragesTable extends Table
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

        $this->setTable('trainee_score_averages');

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
        // Auto-increment primary key: the database supplies it, so no form
        // ever sends one. Requiring it on create made save() refuse every add
        // while edit carried on working, and refuse it the way save() always
        // does - returning false with the reason inside the entity, where the
        // controller's generic "could not be saved" never showed it. See
        // bin/check-create-validation.php, which is what found this.
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->decimal('score_average')
            ->requirePresence('score_average', 'create')
            ->notEmptyString('score_average');

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
