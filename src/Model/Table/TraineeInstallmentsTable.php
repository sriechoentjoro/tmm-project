<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TraineeInstallments Model
 *
 * @property \App\Model\Table\TraineesTable&\Cake\ORM\Association\BelongsTo $Trainees
 * @property \App\Model\Table\MasterTransactionCategoriesTable&\Cake\ORM\Association\BelongsTo $MasterTransactionCategories
 * @property \App\Model\Table\MasterCurrenciesTable&\Cake\ORM\Association\BelongsTo $MasterCurrencies
 *
 * @method \App\Model\Entity\TraineeInstallment get($primaryKey, $options = [])
 * @method \App\Model\Entity\TraineeInstallment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TraineeInstallment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TraineeInstallment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeInstallment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TraineeInstallment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeInstallment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TraineeInstallment findOrCreate($search, callable $callback = null, $options = [])
 */
class TraineeInstallmentsTable extends Table
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

        $this->setTable('trainee_installments');

        $this->belongsTo('Trainees', [
            'foreignKey' => 'trainee_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterTransactionCategories', [
            'foreignKey' => 'master_transaction_category_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterCurrencies', [
            'foreignKey' => 'master_currency_id',
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
            ->integer('payment_amount')
            ->requirePresence('payment_amount', 'create')
            ->notEmptyString('payment_amount');

        $validator
            ->date('payment_date')
            ->requirePresence('payment_date', 'create')
            ->notEmptyDate('payment_date');

        $validator
            ->integer('full_payment_amount')
            ->requirePresence('full_payment_amount', 'create')
            ->notEmptyString('full_payment_amount');

        $validator
            ->integer('payment_accummulated')
            ->requirePresence('payment_accummulated', 'create')
            ->notEmptyString('payment_accummulated');

        $validator
            ->integer('unpaid_amount')
            ->requirePresence('unpaid_amount', 'create')
            ->notEmptyString('unpaid_amount');

        $validator
            ->boolean('is_paid_off')
            ->notEmptyString('is_paid_off');

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
        $rules->add($rules->existsIn(['master_transaction_category_id'], 'MasterTransactionCategories'));
        $rules->add($rules->existsIn(['master_currency_id'], 'MasterCurrencies'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainee_accountings';
    }
}
