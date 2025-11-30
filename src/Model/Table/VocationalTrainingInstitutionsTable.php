<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VocationalTrainingInstitutions Model
 *
 * @property \App\Model\Table\MasterPropinsisTable&\Cake\ORM\Association\BelongsTo $MasterPropinsis
 * @property \App\Model\Table\MasterKabupatensTable&\Cake\ORM\Association\BelongsTo $MasterKabupatens
 * @property \App\Model\Table\MasterKecamatansTable&\Cake\ORM\Association\BelongsTo $MasterKecamatans
 * @property \App\Model\Table\MasterKelurahansTable&\Cake\ORM\Association\BelongsTo $MasterKelurahans
 * @property \App\Model\Table\VocationalTrainingInstitutionStoriesTable&\Cake\ORM\Association\HasMany $VocationalTrainingInstitutionStories
 *
 * @method \App\Model\Entity\VocationalTrainingInstitution get($primaryKey, $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\VocationalTrainingInstitution findOrCreate($search, callable $callback = null, $options = [])
 */
class VocationalTrainingInstitutionsTable extends Table
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

        $this->setTable('vocational_training_institutions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('MasterPropinsis', [
            'foreignKey' => 'master_propinsi_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterKabupatens', [
            'foreignKey' => 'master_kabupaten_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterKecamatans', [
            'foreignKey' => 'master_kecamatan_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterKelurahans', [
            'foreignKey' => 'master_kelurahan_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('VocationalTrainingInstitutionStories', [
            'foreignKey' => 'vocational_training_institution_id',
            'strategy' => 'select',
        ]);
        
        $this->setEntityClass('App\Model\Entity\VocationalTrainingInstitution');
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
            ->boolean('is_special_skill_support_institution')
            ->notEmptyString('is_special_skill_support_institution');

        $validator
            ->scalar('abbreviation')
            ->maxLength('abbreviation', 11)
            ->allowEmptyString('abbreviation');

        $validator
            ->scalar('name')
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('address')
            ->maxLength('address', 256)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('post_code')
            ->maxLength('post_code', 6)
            ->allowEmptyString('post_code');

        $validator
            ->scalar('director')
            ->maxLength('director', 256)
            ->allowEmptyString('director');

        $validator
            ->scalar('director_katakana')
            ->maxLength('director_katakana', 256)
            ->allowEmptyString('director_katakana');

        $validator
            ->scalar('mou_file')
            ->maxLength('mou_file', 256)
            ->requirePresence('mou_file', 'create')
            ->notEmptyFile('mou_file');
        
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
        $rules->add($rules->existsIn(['master_propinsi_id'], 'MasterPropinsis'));
        $rules->add($rules->existsIn(['master_kabupaten_id'], 'MasterKabupatens'));
        $rules->add($rules->existsIn(['master_kecamatan_id'], 'MasterKecamatans'));
        $rules->add($rules->existsIn(['master_kelurahan_id'], 'MasterKelurahans'));
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
