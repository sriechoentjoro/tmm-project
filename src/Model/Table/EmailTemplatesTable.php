<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EmailTemplates Model
 *
 * @method \App\Model\Entity\EmailTemplate get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmailTemplate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EmailTemplate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmailTemplate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmailTemplate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmailTemplate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EmailTemplate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EmailTemplate findOrCreate($search, callable $callback = null, $options = [])
 */
class EmailTemplatesTable extends Table
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

        $this->setTable('email_templates');
        $this->setDisplayField('template_key');
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
            ->requirePresence('template_key', 'create')
            ->notEmptyString('template_key')
            ->add('template_key', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('subject')
            ->maxLength('subject', 255)
            ->requirePresence('subject', 'create')
            ->notEmptyString('subject');

        $validator
            ->scalar('body_html')
            ->requirePresence('body_html', 'create')
            ->notEmptyString('body_html');

        $validator
            ->scalar('body_text')
            ->allowEmptyString('body_text');

        $validator
            ->scalar('variables')
            ->allowEmptyString('variables');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->isUnique(['template_key']));

        return $rules;
    }

    /**
     * Get template by key
     *
     * @param string $templateKey Template key
     * @return \App\Model\Entity\EmailTemplate|null
     */
    public function getTemplate($templateKey)
    {
        return $this->find()
            ->where([
                'template_key' => $templateKey,
                // 1, not true. Where is_active is TINYINT(1) the driver
                // reflects it as boolean and either binds; where it is a plain
                // INT it reflects as integer, and IntegerType refuses a boolean
                // outright - "Cannot convert value of type `boolean` to
                // integer" - so the lookup throws and no email is ever found.
                // An integer binds against both.
                'is_active' => 1,
            ])
            ->first();
    }

    /**
     * Whether a template body should be wrapped in the branded letterhead.
     *
     * A body that already opens an <html> document is a whole email, and
     * wrapping it would nest one document inside another. Anything shorter is a
     * message, and the letterhead belongs around it.
     *
     * Static and public because two places have to agree: EmailComponent when
     * it sends, and EmailTemplatesController when it renders the preview beside
     * the editor. A preview that guessed differently from the sender would be a
     * picture of an email nobody receives.
     *
     * @param string|null $bodyHtml The template's HTML body.
     * @return bool
     */
    public static function wrapsInLayout($bodyHtml)
    {
        return stripos((string)$bodyHtml, '<html') === false;
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
