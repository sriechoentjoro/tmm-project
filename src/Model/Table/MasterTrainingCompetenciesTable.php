<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterTrainingCompetencies Model
 *
 * @property \App\Model\Table\TraineeScoreAveragesTable&\Cake\ORM\Association\HasMany $TraineeScoreAverages
 * @property \App\Model\Table\TraineeTrainingTestScoresTable&\Cake\ORM\Association\HasMany $TraineeTrainingTestScores
 *
 * @method \App\Model\Entity\MasterTrainingCompetency get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTrainingCompetency findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterTrainingCompetenciesTable extends Table
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

        $this->setTable('master_training_competencies');
        $this->setDisplayField('title');

        $this->hasMany('TraineeScoreAverages', [
            'foreignKey' => 'master_training_competency_id',
            'strategy' => 'select',
        ]);
        $this->hasMany('TraineeTrainingTestScores', [
            'foreignKey' => 'master_training_competency_id',
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
        $validator
            ->integer('id')
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

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
