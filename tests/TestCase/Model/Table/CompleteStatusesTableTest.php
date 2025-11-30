<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CompleteStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CompleteStatusesTable Test Case
 */
class CompleteStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CompleteStatusesTable
     */
    public $CompleteStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CompleteStatuses',
        'app.ActionPlanTasks',
        'app.ActionPlans',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CompleteStatuses') ? [] : ['className' => CompleteStatusesTable::class];
        $this->CompleteStatuses = TableRegistry::getTableLocator()->get('CompleteStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CompleteStatuses);

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
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
