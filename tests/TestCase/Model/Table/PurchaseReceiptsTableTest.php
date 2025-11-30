<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PurchaseReceiptsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PurchaseReceiptsTable Test Case
 */
class PurchaseReceiptsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PurchaseReceiptsTable
     */
    public $PurchaseReceipts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PurchaseReceipts',
        'app.Suppliers',
        'app.ApprovalStatuses',
        'app.ApprovedByPersonnels',
        'app.Personnels',
        'app.PurchaseReceiptItems',
        'app.StockIncomings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PurchaseReceipts') ? [] : ['className' => PurchaseReceiptsTable::class];
        $this->PurchaseReceipts = TableRegistry::getTableLocator()->get('PurchaseReceipts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PurchaseReceipts);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
