<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterCurrencies Model
 *
 * @property \App\Model\Table\TraineeInstallmentsTable&\Cake\ORM\Association\HasMany $TraineeInstallments
 *
 * @method \App\Model\Entity\MasterCurrency get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterCurrency newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterCurrency[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCurrency saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCurrency patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterCurrenciesTable extends Table
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

        $this->setTable('master_currencies');
        $this->setDisplayField('title');

        $this->hasMany('TraineeInstallments', [
            'foreignKey' => 'master_currency_id',
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
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        $validator
            ->scalar('currency_code')
            ->maxLength('currency_code', 255)
            ->allowEmptyString('currency_code');

        $validator
            ->scalar('country')
            ->maxLength('country', 255)
            ->allowEmptyString('country');

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
