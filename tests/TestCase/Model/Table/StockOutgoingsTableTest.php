<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StockOutgoingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StockOutgoingsTable Test Case
 */
class StockOutgoingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StockOutgoingsTable
     */
    public $StockOutgoings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.StockOutgoings',
        'app.Inventories',
        'app.StockIncomings',
        'app.Vehicles',
        'app.ApprovalStatuses',
        'app.ApprovedByPersonnels',
        'app.Personnels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StockOutgoings') ? [] : ['className' => StockOutgoingsTable::class];
        $this->StockOutgoings = TableRegistry::getTableLocator()->get('StockOutgoings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StockOutgoings);

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
