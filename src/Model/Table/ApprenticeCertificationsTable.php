<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeCertifications Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 *
 * @method \App\Model\Entity\ApprenticeCertification get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeCertification newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeCertification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCertification|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeCertification saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeCertification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCertification[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeCertification findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeCertificationsTable extends Table
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

        $this->setTable('apprentice_certifications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 256)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('institution_name')
            ->maxLength('institution_name', 255)
            ->requirePresence('institution_name', 'create')
            ->notEmptyString('institution_name');

        $validator
            ->date('certification_date')
            ->allowEmptyDate('certification_date');

        $validator
            ->scalar('detail')
            ->allowEmptyString('detail');

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
        $rules->add($rules->existsIn(['apprentice_id'], 'Apprentices'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentices';
    }
}
