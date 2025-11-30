<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApprovalMatrixFixture
 */
class ApprovalMatrixFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'approval_matrix';
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'matrix_name' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'e.g. Standard Purchase, Emergency Purchase', 'precision' => null, 'fixed' => null],
        'reference_type' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'PurchaseReceipt, StockOutgoing', 'precision' => null, 'fixed' => null],
        'is_default' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => 'Default matrix untuk reference_type ini', 'precision' => null],
        'priority' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '100', 'comment' => 'Lower = higher priority', 'precision' => null, 'autoIncrement' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'idx_reference' => ['type' => 'index', 'columns' => ['reference_type'], 'length' => []],
            'idx_active' => ['type' => 'index', 'columns' => ['active'], 'length' => []],
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
                'matrix_name' => 'Lorem ipsum dolor sit amet',
                'reference_type' => 'Lorem ipsum dolor sit amet',
                'is_default' => 1,
                'priority' => 1,
                'active' => 1,
                'created' => '2025-11-14 18:28:24',
                'modified' => '2025-11-14 18:28:24',
            ],
        ];
        parent::init();
    }
}
