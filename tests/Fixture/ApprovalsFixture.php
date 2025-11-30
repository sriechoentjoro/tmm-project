<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApprovalsFixture
 */
class ApprovalsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'reference_type' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'PurchaseReceipt, StockOutgoing, dll', 'precision' => null, 'fixed' => null],
        'reference_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'ID dari transaksi (NO FK)', 'precision' => null, 'autoIncrement' => null],
        'approval_level' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '1=First, 2=Second, dst', 'precision' => null],
        'approval_status_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '2', 'comment' => 'Reference to approval_statuses (NO FK)', 'precision' => null, 'autoIncrement' => null],
        'personnel_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Personnel yang approve/reject (NO FK)', 'precision' => null, 'autoIncrement' => null],
        'submitted_date' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'Kapan diajukan', 'precision' => null],
        'approved_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Kapan di-approve/reject', 'precision' => null],
        'notes' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Catatan approver', 'precision' => null],
        'rejection_reason' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Alasan reject', 'precision' => null],
        'approval_token' => ['type' => 'string', 'length' => 64, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Unique token untuk approval link', 'precision' => null, 'fixed' => null],
        'token_expires' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Token expiry datetime', 'precision' => null],
        'email_sent' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => 'Email notification sent', 'precision' => null],
        'email_sent_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'idx_reference' => ['type' => 'index', 'columns' => ['reference_type', 'reference_id'], 'length' => []],
            'idx_status' => ['type' => 'index', 'columns' => ['approval_status_id'], 'length' => []],
            'idx_approver' => ['type' => 'index', 'columns' => ['personnel_id'], 'length' => []],
            'idx_submitted' => ['type' => 'index', 'columns' => ['submitted_date'], 'length' => []],
            'idx_approved' => ['type' => 'index', 'columns' => ['approved_date'], 'length' => []],
            'idx_token' => ['type' => 'index', 'columns' => ['approval_token'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
                'reference_type' => 'Lorem ipsum dolor sit amet',
                'reference_id' => 1,
                'approval_level' => 1,
                'approval_status_id' => 1,
                'personnel_id' => 1,
                'submitted_date' => '2025-11-14 18:28:54',
                'approved_date' => '2025-11-14 18:28:54',
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'rejection_reason' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'approval_token' => 'Lorem ipsum dolor sit amet',
                'token_expires' => '2025-11-14 18:28:54',
                'email_sent' => 1,
                'email_sent_date' => '2025-11-14 18:28:54',
                'created' => '2025-11-14 18:28:54',
                'modified' => '2025-11-14 18:28:54',
            ],
        ];
        parent::init();
    }
}
