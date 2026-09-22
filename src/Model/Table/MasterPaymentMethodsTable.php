<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterPaymentMethods Model
 *
 * @method \App\Model\Entity\MasterPaymentMethod get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterPaymentMethod findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterPaymentMethodsTable extends Table
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

        $this->setTable('master_payment_methods');
        $this->setDisplayField('title');
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
            ->maxLength('title', 256)
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
