<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterTransactionCategories Model
 *
 * @property \App\Model\Table\TraineeInstallmentsTable&\Cake\ORM\Association\HasMany $TraineeInstallments
 *
 * @method \App\Model\Entity\MasterTransactionCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterTransactionCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterTransactionCategoriesTable extends Table
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

        $this->setTable('master_transaction_categories');
        $this->setDisplayField('title');

        $this->hasMany('TraineeInstallments', [
            'foreignKey' => 'master_transaction_category_id',
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
            ->maxLength('title', 50)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        return $validator;
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
