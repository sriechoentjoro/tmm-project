<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminApprovalQueue Model
 *
 * Manages approval workflow for stakeholder registrations
 * Tracks pending approvals, rejections, and review history
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $ReviewedByUsers
 */
class AdminApprovalQueueTable extends Table
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

        $this->setTable('admin_approval_queue');
        $this->setDisplayField('approval_type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'submitted_at' => 'new'
                ]
            ]
        ]);

        // Association to Users table (admin who reviewed)
        $this->belongsTo('ReviewedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'reviewed_by',
            'joinType' => 'LEFT',
            'propertyName' => 'reviewed_by_user'
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
            ->scalar('approval_type')
            ->maxLength('approval_type', 50)
            ->requirePresence('approval_type', 'create')
            ->notEmptyString('approval_type')
            ->inList('approval_type', [
                'registration',
                'verification',
                'profile_update',
                'permission_change',
                'data_deletion',
                'account_closure'
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
            ->scalar('status')
            ->maxLength('status', 20)
            ->requirePresence('status', 'create')
            ->notEmptyString('status')
            ->inList('status', ['pending', 'approved', 'rejected', 'cancelled']);

        $validator
            ->dateTime('submitted_at')
            ->allowEmptyDateTime('submitted_at');

        $validator
            ->integer('reviewed_by')
            ->allowEmptyString('reviewed_by');

        $validator
            ->dateTime('reviewed_at')
            ->allowEmptyDateTime('reviewed_at');

        $validator
            ->scalar('rejection_reason')
            ->allowEmptyString('rejection_reason');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        return $validator;
    }

    /**
     * Find pending approvals
     *
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Options array
     * @return \Cake\ORM\Query
     */
    public function findPending($query, array $options)
    {
        return $query
            ->where(['AdminApprovalQueue.status' => 'pending'])
            ->order(['AdminApprovalQueue.submitted_at' => 'ASC'])
            ->contain(['ReviewedByUsers']);
    }

    /**
     * Find approvals by stakeholder type
     *
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Options array
     * @return \Cake\ORM\Query
     */
    public function findByStakeholderType($query, array $options)
    {
        $type = isset($options['type']) ? $options['type'] : null;
        
        if ($type) {
            return $query->where(['AdminApprovalQueue.stakeholder_type' => $type]);
        }
        
        return $query;
    }

    /**
     * Submit for approval
     *
     * @param string $approvalType Type of approval request
     * @param string $stakeholderType Type of stakeholder
     * @param int $stakeholderId Stakeholder ID
     * @param string|null $notes Optional notes
     * @return bool|int Approval queue ID or false on failure
     */
    public function submitForApproval($approvalType, $stakeholderType, $stakeholderId, $notes = null)
    {
        // Check if already in queue
        $existing = $this->find()
            ->where([
                'approval_type' => $approvalType,
                'stakeholder_type' => $stakeholderType,
                'stakeholder_id' => $stakeholderId,
                'status' => 'pending'
            ])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        $approval = $this->newEntity([
            'approval_type' => $approvalType,
            'stakeholder_type' => $stakeholderType,
            'stakeholder_id' => $stakeholderId,
            'status' => 'pending',
            'notes' => $notes
        ]);

        if ($this->save($approval)) {
            return $approval->id;
        }

        return false;
    }

    /**
     * Approve request
     *
     * @param int $approvalId Approval queue ID
     * @param int $reviewedBy Admin user ID
     * @param string|null $notes Optional notes
     * @return bool Success status
     */
    public function approve($approvalId, $reviewedBy, $notes = null)
    {
        $approval = $this->get($approvalId);
        
        if (!$approval || $approval->status !== 'pending') {
            return false;
        }

        $approval->status = 'approved';
        $approval->reviewed_by = $reviewedBy;
        $approval->reviewed_at = date('Y-m-d H:i:s');
        if ($notes) {
            $approval->notes = $approval->notes ? $approval->notes . "\n\n" . $notes : $notes;
        }

        return (bool)$this->save($approval);
    }

    /**
     * Reject request
     *
     * @param int $approvalId Approval queue ID
     * @param int $reviewedBy Admin user ID
     * @param string $reason Rejection reason
     * @param string|null $notes Optional notes
     * @return bool Success status
     */
    public function reject($approvalId, $reviewedBy, $reason, $notes = null)
    {
        $approval = $this->get($approvalId);
        
        if (!$approval || $approval->status !== 'pending') {
            return false;
        }

        $approval->status = 'rejected';
        $approval->reviewed_by = $reviewedBy;
        $approval->reviewed_at = date('Y-m-d H:i:s');
        $approval->rejection_reason = $reason;
        if ($notes) {
            $approval->notes = $approval->notes ? $approval->notes . "\n\n" . $notes : $notes;
        }

        return (bool)$this->save($approval);
    }

    /**
     * Get pending count by type
     *
     * @param string|null $stakeholderType Optional stakeholder type filter
     * @return int Count of pending approvals
     */
    public function getPendingCount($stakeholderType = null)
    {
        $query = $this->find()->where(['status' => 'pending']);
        
        if ($stakeholderType) {
            $query->where(['stakeholder_type' => $stakeholderType]);
        }
        
        return $query->count();
    }
}
