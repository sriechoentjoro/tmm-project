<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PurchaseRequestItemsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PurchaseRequestItemsTable Test Case
 */
class PurchaseRequestItemsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PurchaseRequestItemsTable
     */
    public $PurchaseRequestItems;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PurchaseRequestItems',
        'app.PurchaseRequests',
        'app.Inventories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PurchaseRequestItems') ? [] : ['className' => PurchaseRequestItemsTable::class];
        $this->PurchaseRequestItems = TableRegistry::getTableLocator()->get('PurchaseRequestItems', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PurchaseRequestItems);

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

    /**
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
