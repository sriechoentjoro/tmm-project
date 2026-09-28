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

    /**
     * The averages this table claims, worked out from the test scores.
     *
     * A row here holds a trainee's average for one competency and the grade
     * that average earns. Both are typed into a form. Neither is recomputed
     * when a test score is entered, corrected or deleted, so a row is right on
     * the day somebody fills it in and drifts from then on - and nothing on
     * the page says it is a note rather than a calculation.
     *
     * The application already computes an average elsewhere and disagrees with
     * itself as a result: ApprenticesTable::departureEvidenceFor() averages
     * trainee_training_test_scores directly, pooling every competency, and
     * that is the figure the departure screen shows. This table's per-
     * competency figures are read by nothing but its own screen.
     *
     * The grade is derivable too. master_training_test_score_grades holds the
     * bands, so the grade for an average is simply the band it falls in -
     * there is nothing for a person to decide.
     *
     * @param array|null $traineeIds Limit to these trainees, or null for all.
     * @return array "traineeId:competencyId" => ['average' => float, 'grade_id' => int|null]
     */
    public function standingFor(array $traineeIds = null)
    {
        try {
            $scores = \Cake\ORM\TableRegistry::getTableLocator()->get('TraineeTrainingTestScores');
            $query = $scores->find();
            $query->select([
                'trainee_id' => 'trainee_id',
                'master_training_competency_id' => 'master_training_competency_id',
                'average' => $query->func()->avg('score'),
            ]);
            if ($traineeIds !== null) {
                $traineeIds = array_values(array_unique(array_map('intval', $traineeIds)));
                if (!$traineeIds) {
                    return [];
                }
                $query->where(['trainee_id IN' => $traineeIds]);
            }
            $rows = $query->group(['trainee_id', 'master_training_competency_id'])
                ->enableHydration(false)->toArray();
        } catch (\Exception $e) {
            // Without the scores there is nothing to compare against, and a
            // zero here would read as a trainee who scored nothing.
            return [];
        }

        $bands = $this->gradeBands();

        $standing = [];
        foreach ($rows as $row) {
            if ($row['average'] === null) {
                continue;
            }
            $average = round((float)$row['average'], 1);
            $standing[$row['trainee_id'] . ':' . $row['master_training_competency_id']] = [
                'average' => $average,
                'grade_id' => $this->gradeFor($average, $bands),
            ];
        }

        return $standing;
    }

    /**
     * The score bands, lowest first.
     *
     * @return array [['id' => int, 'min' => float|null, 'max' => float|null], ...]
     */
    protected function gradeBands()
    {
        try {
            $rows = $this->MasterTrainingTestScoreGrades->find()
                ->enableHydration(false)->toArray();
        } catch (\Exception $e) {
            return [];
        }

        $bands = [];
        foreach ($rows as $row) {
            $bands[] = [
                'id' => (int)$row['id'],
                'min' => $row['min_score'] === null ? null : (float)$row['min_score'],
                'max' => $row['max_score'] === null ? null : (float)$row['max_score'],
            ];
        }
        usort($bands, function ($a, $b) {
            return ($a['min'] ?? -INF) <=> ($b['min'] ?? -INF);
        });

        return $bands;
    }

    /**
     * Which band an average falls in.
     *
     * An open end counts as unbounded, which is how a top grade is usually
     * written. An average in no band at all returns null rather than the
     * nearest one: a grade nobody defined is a gap in the bands, and guessing
     * at it would hide that.
     *
     * @return int|null
     */
    protected function gradeFor($average, array $bands)
    {
        foreach ($bands as $band) {
            if ($band['min'] !== null && $average < $band['min']) {
                continue;
            }
            if ($band['max'] !== null && $average > $band['max']) {
                continue;
            }

            return $band['id'];
        }

        return null;
    }

    /**
     * What this row says that the scores do not.
     *
     * @param \Cake\Datasource\EntityInterface $row A stored average.
     * @param array $standing One entry from standingFor().
     * @return array [field => [stored, real]]
     */
    public function disagreements($row, array $standing)
    {
        $out = [];
        if (abs((float)$row->get('score_average') - (float)$standing['average']) > 0.05) {
            $out['score_average'] = [(float)$row->get('score_average'), $standing['average']];
        }
        // A grade the bands do not cover is not a disagreement with the row -
        // it is a gap in the bands, and saying the row is wrong would point at
        // the wrong thing.
        if ($standing['grade_id'] !== null
            && (int)$row->get('master_training_test_score_grade_id') !== $standing['grade_id']) {
            $out['master_training_test_score_grade_id'] = [
                (int)$row->get('master_training_test_score_grade_id'), $standing['grade_id']];
        }

        return $out;
    }

    /**
     * Write the computed average and grade onto a row.
     *
     * @param int $traineeId Trainee id.
     * @param int $competencyId Competency id.
     * @return array|null What was written, or null when there was nothing to write.
     */
    public function refresh($traineeId, $competencyId)
    {
        $key = (int)$traineeId . ':' . (int)$competencyId;
        $standing = $this->standingFor([$traineeId]);
        if (!isset($standing[$key])) {
            return null;
        }

        $row = $this->find()->where([
            'trainee_id' => (int)$traineeId,
            'master_training_competency_id' => (int)$competencyId,
        ])->first();
        if (!$row) {
            return null;
        }

        $row->set('score_average', $standing[$key]['average']);
        if ($standing[$key]['grade_id'] !== null) {
            $row->set('master_training_test_score_grade_id', $standing[$key]['grade_id']);
        }

        if (!$this->save($row, ['checkRules' => false, 'validate' => false])) {
            \Cake\Log\Log::error(sprintf('score average refresh failed for trainee %d competency %d: %s',
                $traineeId, $competencyId, json_encode($row->getErrors())));

            return null;
        }

        return $standing[$key];
    }
}
