<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StakeholderActivities Model
 *
 * Tracks all stakeholder activities for audit purposes
 * Logs user actions, admin actions, and system events
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Admins
 */
class StakeholderActivitiesTable extends Table
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

        $this->setTable('stakeholder_activities');
        $this->setDisplayField('description');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        // Association to Users table (stakeholder user)
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
            'propertyName' => 'user'
        ]);

        // Association to Users table (admin who performed action)
        $this->belongsTo('Admins', [
            'className' => 'Users',
            'foreignKey' => 'admin_id',
            'joinType' => 'LEFT',
            'propertyName' => 'admin'
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
            ->scalar('activity_type')
            ->maxLength('activity_type', 50)
            ->requirePresence('activity_type', 'create')
            ->notEmptyString('activity_type')
            ->inList('activity_type', [
                'registration',
                'verification',
                'login',
                'logout',
                'profile_update',
                'password_change',
                'data_export',
                'candidate_add',
                'candidate_edit',
                'candidate_delete',
                'trainee_add',
                'trainee_edit',
                'trainee_delete',
                'admin_approval',
                'admin_rejection',
                'suspension',
                'activation',
                'permission_change'
            ]);

        $validator
            ->scalar('stakeholder_type')
            ->maxLength('stakeholder_type', 50)
            ->requirePresence('stakeholder_type', 'create')
            ->notEmptyString('stakeholder_type')
            ->inList('stakeholder_type', [
                'lpk',
                'special_skill',
                'acceptance_org',
                'cooperative_assoc'
            ]);

        $validator
            ->integer('stakeholder_id')
            ->requirePresence('stakeholder_id', 'create')
            ->notEmptyString('stakeholder_id');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->integer('admin_id')
            ->allowEmptyString('admin_id');

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->allowEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->maxLength('user_agent', 500)
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('additional_data')
            ->allowEmptyString('additional_data');

        return $validator;
    }

    /**
     * Find activities by stakeholder type
     *
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Options array
     * @return \Cake\ORM\Query
     */
    public function findByStakeholderType($query, array $options)
    {
        $type = isset($options['type']) ? $options['type'] : null;
        
        if ($type) {
            return $query->where(['StakeholderActivities.stakeholder_type' => $type]);
        }
        
        return $query;
    }

    /**
     * Find recent activities
     *
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Options array
     * @return \Cake\ORM\Query
     */
    public function findRecent($query, array $options)
    {
        $limit = isset($options['limit']) ? $options['limit'] : 50;
        
        return $query
            ->order(['StakeholderActivities.created' => 'DESC'])
            ->limit($limit)
            ->contain(['Users', 'Admins']);
    }

    /**
     * Log activity
     *
     * @param string $activityType Type of activity
     * @param string $stakeholderType Type of stakeholder
     * @param int $stakeholderId Stakeholder ID
     * @param string $description Activity description
     * @param array $additionalData Additional data as array
     * @param int|null $userId User ID (if applicable)
     * @param int|null $adminId Admin ID (if applicable)
     * @return bool Success status
     */
    public function logActivity($activityType, $stakeholderType, $stakeholderId, $description, $additionalData = array(), $userId = null, $adminId = null)
    {
        $activity = $this->newEntity([
            'activity_type' => $activityType,
            'stakeholder_type' => $stakeholderType,
            'stakeholder_id' => $stakeholderId,
            'description' => $description,
            'user_id' => $userId,
            'admin_id' => $adminId,
            'ip_address' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'additional_data' => !empty($additionalData) ? json_encode($additionalData) : null
        ]);

        return (bool)$this->save($activity);
    }
}
