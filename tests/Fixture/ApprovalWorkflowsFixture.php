<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApprovalWorkflowsFixture
 */
class ApprovalWorkflowsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'workflow_name' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'reference_type' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'PurchaseReceipt, StockOutgoing', 'precision' => null, 'fixed' => null],
        'approval_level' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'Level approval (1, 2, 3)', 'precision' => null],
        'approver_role' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Supervisor, Manager, Director', 'precision' => null, 'fixed' => null],
        'approver_personnel_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Specific approver (NO FK), NULL = any with role', 'precision' => null, 'autoIncrement' => null],
        'min_amount' => ['type' => 'decimal', 'length' => 15, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Min amount untuk level ini'],
        'max_amount' => ['type' => 'decimal', 'length' => 15, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Max amount (NULL=unlimited)'],
        'send_email' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => 'Send email notification', 'precision' => null],
        'send_whatsapp' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => 'Send WhatsApp notification', 'precision' => null],
        'is_required' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '1=Wajib, 0=Optional', 'precision' => null],
        'auto_approve' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => 'Auto approve if conditions met', 'precision' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'idx_reference' => ['type' => 'index', 'columns' => ['reference_type'], 'length' => []],
            'idx_amount_range' => ['type' => 'index', 'columns' => ['min_amount', 'max_amount'], 'length' => []],
            'idx_approver' => ['type' => 'index', 'columns' => ['approver_personnel_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'unique_workflow' => ['type' => 'unique', 'columns' => ['reference_type', 'approval_level'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'workflow_name' => 'Lorem ipsum dolor sit amet',
                'reference_type' => 'Lorem ipsum dolor sit amet',
                'approval_level' => 1,
                'approver_role' => 'Lorem ipsum dolor sit amet',
                'approver_personnel_id' => 1,
                'min_amount' => 1.5,
                'max_amount' => 1.5,
                'send_email' => 1,
                'send_whatsapp' => 1,
                'is_required' => 1,
                'auto_approve' => 1,
                'active' => 1,
                'created' => '2025-11-14 18:28:53',
                'modified' => '2025-11-14 18:28:53',
            ],
        ];
        parent::init();
    }
}
