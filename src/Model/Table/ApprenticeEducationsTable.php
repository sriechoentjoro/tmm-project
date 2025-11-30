<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeEducations Model
 *
 * @property \App\Model\Table\ApprenticesTable&\Cake\ORM\Association\BelongsTo $Apprentices
 * @property \App\Model\Table\MasterStratasTable&\Cake\ORM\Association\BelongsTo $MasterStratas
 * @property \App\Model\Table\MasterPropinsisTable&\Cake\ORM\Association\BelongsTo $MasterPropinsis
 * @property \App\Model\Table\MasterKabupatensTable&\Cake\ORM\Association\BelongsTo $MasterKabupatens
 *
 * @method \App\Model\Entity\ApprenticeEducation get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeEducation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeEducation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeEducation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeEducation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeEducation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeEducation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeEducation findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeEducationsTable extends Table
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

        $this->setTable('apprentice_educations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Apprentices', [
            'foreignKey' => 'apprentice_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterStratas', [
            'foreignKey' => 'master_strata_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterPropinsis', [
            'foreignKey' => 'master_propinsi_id',
            'strategy' => 'select',
        ]);
        $this->belongsTo('MasterKabupatens', [
            'foreignKey' => 'master_kabupaten_id',
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->date('college_entry_date')
            ->allowEmptyDate('college_entry_date');

        $validator
            ->date('college_graduate_date')
            ->allowEmptyDate('college_graduate_date');

        $validator
            ->scalar('college_name')
            ->maxLength('college_name', 255)
            ->requirePresence('college_name', 'create')
            ->notEmptyString('college_name');

        $validator
            ->scalar('college_major')
            ->maxLength('college_major', 255)
            ->allowEmptyString('college_major');

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
        $rules->add($rules->existsIn(['master_strata_id'], 'MasterStratas'));
        $rules->add($rules->existsIn(['master_propinsi_id'], 'MasterPropinsis'));
        $rules->add($rules->existsIn(['master_kabupaten_id'], 'MasterKabupatens'));

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
