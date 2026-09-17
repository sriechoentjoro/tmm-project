<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Which institutions an apprentice order has been offered to.
 *
 * Lives on cms_tmm_trainees, beside apprentice_orders. The institution it
 * points at lives on cms_tmm_stakeholders, a different connection, so there is
 * deliberately no association to it: the ORM cannot join across connections,
 * and pretending otherwise produces a belongsTo that fails at query time rather
 * than at definition time. Callers resolve institutions separately, which is
 * how VocationalTrainingInstitutionsController::verify() already counts
 * candidates across the same boundary.
 *
 * lpk_name and lpk_email are snapshots taken when the order was shared, not
 * copies kept in step. They are what the notification was actually addressed
 * to, which is the question anyone looking at an old share is asking - and they
 * survive the institution being renamed or deleted.
 *
 * @property \App\Model\Table\ApprenticeOrdersTable&\Cake\ORM\Association\BelongsTo $ApprenticeOrders
 */
class ApprenticeOrderSharesTable extends Table
{
    /**
     * @param array $config Table configuration.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('apprentice_order_shares');
        $this->setDisplayField('lpk_name');
        $this->setPrimaryKey('id');

        $this->belongsTo('ApprenticeOrders', [
            'foreignKey' => 'apprentice_order_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('apprentice_order_id')
            ->requirePresence('apprentice_order_id', 'create')
            ->notEmptyString('apprentice_order_id');

        $validator
            ->integer('vocational_training_institution_id')
            ->requirePresence('vocational_training_institution_id', 'create')
            ->notEmptyString('vocational_training_institution_id');

        $validator
            ->scalar('status')
            ->inList('status', ['shared', 'cancelled'])
            ->notEmptyString('status');

        $validator
            ->email('lpk_email')
            ->allowEmptyString('lpk_email');

        return $validator;
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules The rules object.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        // One row per order and institution. Sharing an order that was
        // cancelled reopens the same row rather than adding a second, so the
        // history of an offer stays in one place.
        $rules->add($rules->isUnique(
            ['apprentice_order_id', 'vocational_training_institution_id'],
            'This order has already been offered to that institution'
        ));

        return $rules;
    }

    /**
     * The shares of one order, newest first.
     *
     * @param int $orderId The order.
     * @return \Cake\ORM\Query
     */
    public function forOrder($orderId)
    {
        return $this->find()
            ->where(['apprentice_order_id' => $orderId])
            ->order(['created' => 'DESC', 'id' => 'DESC']);
    }

    /**
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainees';
    }
}
