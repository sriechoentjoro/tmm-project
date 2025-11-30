<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StakeholderPermissions Model
 *
 * Manages permission matrix for all stakeholder types
 * Controls access to specific features and data operations
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $GrantedByUsers
 */
class StakeholderPermissionsTable extends Table
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

        $this->setTable('stakeholder_permissions');
        $this->setDisplayField('permission_key');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Association to Users table (admin who granted permission)
        $this->belongsTo('GrantedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'granted_by',
            'joinType' => 'LEFT',
            'propertyName' => 'granted_by_user'
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
            ->scalar('permission_key')
            ->maxLength('permission_key', 100)
            ->requirePresence('permission_key', 'create')
            ->notEmptyString('permission_key');

        $validator
            ->boolean('permission_value')
            ->requirePresence('permission_value', 'create')
            ->notEmptyString('permission_value');

        $validator
            ->integer('granted_by')
            ->allowEmptyString('granted_by');

        $validator
            ->dateTime('granted_at')
            ->allowEmptyDateTime('granted_at');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        return $validator;
    }

    /**
     * Check if stakeholder has permission
     *
     * @param string $stakeholderType Type of stakeholder
     * @param int $stakeholderId Stakeholder ID
     * @param string $permissionKey Permission key to check
     * @return bool True if has permission
     */
    public function hasPermission($stakeholderType, $stakeholderId, $permissionKey)
    {
        $permission = $this->find()
            ->where([
                'stakeholder_type' => $stakeholderType,
                'stakeholder_id' => $stakeholderId,
                'permission_key' => $permissionKey
            ])
            ->first();

        if ($permission) {
            return (bool)$permission->permission_value;
        }

        // Check for default permission
        return $this->getDefaultPermission($stakeholderType, $permissionKey);
    }

    /**
     * Get default permission value for stakeholder type
     *
     * @param string $stakeholderType Type of stakeholder
     * @param string $permissionKey Permission key
     * @return bool Default permission value
     */
    public function getDefaultPermission($stakeholderType, $permissionKey)
    {
        // Default permissions based on stakeholder type
        $defaults = array(
            'lpk' => array(
                'candidates.view' => true,
                'candidates.add' => true,
                'candidates.edit' => true,
                'candidates.export' => true,
                'candidates.delete' => false,
                'dashboard.view' => true,
                'reports.view' => true
            ),
            'special_skill' => array(
                'trainees.view' => true,
                'trainees.add' => true,
                'trainees.edit' => true,
                'trainees.export' => true,
                'trainees.delete' => false,
                'dashboard.view' => true,
                'reports.view' => true
            ),
            'acceptance_org' => array(
                'apprentices.view' => true,
                'dashboard.view' => true
            ),
            'cooperative_assoc' => array(
                'members.view' => true,
                'dashboard.view' => true
            )
        );

        if (isset($defaults[$stakeholderType]) && isset($defaults[$stakeholderType][$permissionKey])) {
            return $defaults[$stakeholderType][$permissionKey];
        }

        return false;
    }

    /**
     * Grant permission to stakeholder
     *
     * @param string $stakeholderType Type of stakeholder
     * @param int $stakeholderId Stakeholder ID
     * @param string $permissionKey Permission key
     * @param bool $value Permission value (true/false)
     * @param int|null $grantedBy Admin user ID
     * @param string|null $notes Optional notes
     * @return bool Success status
     */
    public function grantPermission($stakeholderType, $stakeholderId, $permissionKey, $value, $grantedBy = null, $notes = null)
    {
        // Check if permission already exists
        $existing = $this->find()
            ->where([
                'stakeholder_type' => $stakeholderType,
                'stakeholder_id' => $stakeholderId,
                'permission_key' => $permissionKey
            ])
            ->first();

        if ($existing) {
            // Update existing permission
            $existing->permission_value = $value;
            $existing->granted_by = $grantedBy;
            $existing->granted_at = date('Y-m-d H:i:s');
            $existing->notes = $notes;
            return (bool)$this->save($existing);
        } else {
            // Create new permission
            $permission = $this->newEntity([
                'stakeholder_type' => $stakeholderType,
                'stakeholder_id' => $stakeholderId,
                'permission_key' => $permissionKey,
                'permission_value' => $value,
                'granted_by' => $grantedBy,
                'granted_at' => date('Y-m-d H:i:s'),
                'notes' => $notes
            ]);
            return (bool)$this->save($permission);
        }
    }

    /**
     * Get all permissions for stakeholder
     *
     * @param string $stakeholderType Type of stakeholder
     * @param int $stakeholderId Stakeholder ID
     * @return array Array of permissions
     */
    public function getStakeholderPermissions($stakeholderType, $stakeholderId)
    {
        $permissions = $this->find()
            ->where([
                'stakeholder_type' => $stakeholderType,
                'stakeholder_id' => $stakeholderId
            ])
            ->all();

        $result = array();
        foreach ($permissions as $permission) {
            $result[$permission->permission_key] = (bool)$permission->permission_value;
        }

        return $result;
    }
}
