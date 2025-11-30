<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterKecamatans Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Propinsis
 * @property &\Cake\ORM\Association\BelongsTo $Kabupatens
 *
 * @method \App\Model\Entity\MasterKecamatan get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterKecamatan newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterKecamatan[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterKecamatan|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterKecamatan saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterKecamatan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterKecamatan[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterKecamatan findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterKecamatansTable extends Table
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

        $this->setTable('master_kecamatans');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('MasterPropinsis', [
            'foreignKey' => 'propinsi_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterKabupatens', [
            'foreignKey' => 'kabupaten_id',
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
            ->scalar('kode_propinsi')
            ->maxLength('kode_propinsi', 11)
            ->requirePresence('kode_propinsi', 'create')
            ->notEmptyString('kode_propinsi');

        $validator
            ->scalar('kode_kabupaten')
            ->maxLength('kode_kabupaten', 11)
            ->requirePresence('kode_kabupaten', 'create')
            ->notEmptyString('kode_kabupaten');

        $validator
            ->scalar('kode_kecamatan')
            ->maxLength('kode_kecamatan', 11)
            ->requirePresence('kode_kecamatan', 'create')
            ->notEmptyString('kode_kecamatan');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

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
        $rules->add($rules->existsIn(['propinsi_id'], 'MasterPropinsis'));
        $rules->add($rules->existsIn(['kabupaten_id'], 'MasterKabupatens'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_masters';
    }
}
