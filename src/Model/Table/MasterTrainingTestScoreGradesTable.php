<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterTrainingTestScoreGrades Model
 *
 * @property \App\Model\Table\TraineeScoreAveragesTable&\Cake\ORM\Association\HasMany $TraineeScoreAverages
 * @property \App\Model\Table\TraineeTrainingTestScoresTable&\Cake\ORM\Association\HasMany $TraineeTrainingTestScores
 *
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingTestScoreGrade findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterTrainingTestScoreGradesTable extends Table
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

        $this->setTable('master_training_test_score_grades');
        $this->setDisplayField('title');

        $this->hasMany('TraineeScoreAverages', [
            'foreignKey' => 'master_training_test_score_grade_id',
            'strategy' => 'select',
        ]);
        $this->hasMany('TraineeTrainingTestScores', [
            'foreignKey' => 'master_training_test_score_grade_id',
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
            ->scalar('title')
            ->maxLength('title', 2)
            ->allowEmptyString('title');

        $validator
            ->integer('min_score')
            ->allowEmptyString('min_score');

        $validator
            ->integer('max_score')
            ->allowEmptyString('max_score');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        return $validator;
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
