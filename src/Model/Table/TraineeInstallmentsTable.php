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
     * Recompute one trainee's running totals from their payments.
     *
     * Every row carries the accumulated total and the outstanding balance as
     * they stood when it was written, and each was worked out from the row
     * before it. That is fine while payments are only ever appended - and it
     * quietly falls apart the moment one is deleted or edited from the middle,
     * because nothing walked forward to fix the rows after it. The tracking
     * page reads the LAST row, so a trainee could be shown as owing a figure
     * that none of their payments add up to.
     *
     * This walks the chain in id order and rewrites it: the opening row keeps
     * the owing cost, every payment adds to the accumulated total, and the
     * outstanding balance follows. It is the one authority for those three
     * fields, so add, edit and delete all end by calling it rather than each
     * keeping its own arithmetic.
     *
     * Saved without validation or rules: these are derived fields, not user
     * input, and a legacy row with some unrelated problem must not be able to
     * leave the chain half-rebuilt.
     *
     * @param int $traineeId Trainee id.
     * @return array ['rows' => n, 'full' => n, 'accumulated' => n, 'unpaid' => n]
     */
    public function rebuildChain($traineeId)
    {
        $traineeId = (int)$traineeId;
        $summary = ['rows' => 0, 'full' => 0, 'accumulated' => 0, 'unpaid' => 0];
        if (!$traineeId) {
            return $summary;
        }

        $rows = $this->find()
            ->where(['trainee_id' => $traineeId])
            ->order(['id' => 'ASC'])
            ->toArray();

        if (!$rows) {
            return $summary;
        }

        // The owing cost is set once, on the opening row. Later rows carry a
        // copy of it; the opening row is the one to trust, and where an older
        // row somehow has a larger figure the largest is taken rather than
        // silently shrinking what the trainee owes.
        $full = 0;
        foreach ($rows as $row) {
            $full = max($full, (int)$row->full_payment_amount);
        }

        $accumulated = 0;
        foreach ($rows as $row) {
            $accumulated += max(0, (int)$row->payment_amount);
            $unpaid = max(0, $full - $accumulated);

            $row->set('full_payment_amount', $full);
            $row->set('payment_accummulated', $accumulated);
            $row->set('unpaid_amount', $unpaid);
            $row->set('is_paid_off', $unpaid === 0 ? 1 : 0);

            if ($row->isDirty()) {
                if (!$this->save($row, ['checkRules' => false, 'validate' => false])) {
                    \Cake\Log\Log::error(sprintf(
                        'rebuildChain could not save installment %d for trainee %d: %s',
                        $row->id,
                        $traineeId,
                        json_encode($row->getErrors())
                    ));
                }
            }

            $summary['rows']++;
        }

        $summary['full'] = $full;
        $summary['accumulated'] = $accumulated;
        $summary['unpaid'] = max(0, $full - $accumulated);

        return $summary;
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
