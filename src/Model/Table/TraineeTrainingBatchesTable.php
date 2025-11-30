<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeTrainingBatches Model
 *
 * @method \App\Model\Entity\TraineeTrainingBatch get($primaryKey, $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeTrainingBatch findOrCreate($search, callable $callback = null, $options = [])
 */
class TraineeTrainingBatchesTable extends Table
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

        $this->setTable('trainee_training_batches');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('batch_name')
            ->maxLength('batch_name', 255)
            ->allowEmptyString('batch_name');

        $validator
            ->date('departure_plan_date')
            ->allowEmptyDate('departure_plan_date');

        $validator
            ->boolean('is_training_location_moved')
            ->notEmptyString('is_training_location_moved');

        $validator
            ->integer('training_term_of_months')
            ->requirePresence('training_term_of_months', 'create')
            ->notEmptyString('training_term_of_months');

        $validator
            ->scalar('origin_training_location')
            ->maxLength('origin_training_location', 256)
            ->requirePresence('origin_training_location', 'create')
            ->notEmptyString('origin_training_location');

        $validator
            ->scalar('moved_training_location')
            ->maxLength('moved_training_location', 256)
            ->allowEmptyString('moved_training_location');

        $validator
            ->date('origin_start_plan_date')
            ->requirePresence('origin_start_plan_date', 'create')
            ->notEmptyDate('origin_start_plan_date');

        $validator
            ->date('origin_finish_plan_date')
            ->requirePresence('origin_finish_plan_date', 'create')
            ->notEmptyDate('origin_finish_plan_date');

        $validator
            ->date('moved_start_plan_date')
            ->allowEmptyDate('moved_start_plan_date');

        $validator
            ->date('moved_finished_plan_date')
            ->allowEmptyDate('moved_finished_plan_date');

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainee_trainings';
    }
}
