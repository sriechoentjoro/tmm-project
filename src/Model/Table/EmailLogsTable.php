<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EmailLogs Model
 *
 * @method \App\Model\Entity\EmailLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmailLog newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EmailLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmailLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class EmailLogsTable extends Table
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

        $this->setTable('email_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->scalar('template_key')
            ->maxLength('template_key', 50)
            ->allowEmptyString('template_key');

        $validator
            ->email('recipient_email')
            ->requirePresence('recipient_email', 'create')
            ->notEmptyString('recipient_email');

        $validator
            ->scalar('subject')
            ->maxLength('subject', 255)
            ->requirePresence('subject', 'create')
            ->notEmptyString('subject');

        $validator
            ->scalar('body')
            ->allowEmptyString('body');

        $validator
            ->scalar('status')
            ->notEmptyString('status');

        $validator
            ->scalar('error_message')
            ->allowEmptyString('error_message');

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_authentication_authorization';
    }
}
