<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SpecialSkillSupportInstitutions Model
 *
 * @method \App\Model\Entity\SpecialSkillSupportInstitution get($primaryKey, $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SpecialSkillSupportInstitution findOrCreate($search, callable $callback = null, $options = [])
 */
class SpecialSkillSupportInstitutionsTable extends Table
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

        $this->setTable('special_skill_support_institutions');
        $this->setDisplayField('company_name');
        $this->setPrimaryKey('id');
        
        $this->setEntityClass('App\Model\Entity\SpecialSkillSupportInstitution');
        $this->addBehavior('Timestamp');
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
            ->scalar('company_name')
            ->maxLength('company_name', 255)
            ->requirePresence('company_name', 'create')
            ->notEmptyString('company_name');

        $validator
            ->scalar('address')
            ->allowEmptyString('address');

        $validator
            ->scalar('contact_person')
            ->maxLength('contact_person', 255)
            ->allowEmptyString('contact_person');
        
        $validator
            ->email('email')
            ->maxLength('email', 100)
            ->requirePresence('email', 'create')
            ->notEmptyString('email');
        
        $validator
            ->scalar('username')
            ->maxLength('username', 50)
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->alphaNumeric('username', 'Username must be alphanumeric');
        
        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->minLength('password', 8, 'Password must be at least 8 characters', 'create')
            ->allowEmptyString('password'); // Allow empty on update

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
        $rules->add($rules->isUnique(['email'], 'This email is already registered'));
        $rules->add($rules->isUnique(['username'], 'This username is already taken'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_stakeholders';
    }
}
