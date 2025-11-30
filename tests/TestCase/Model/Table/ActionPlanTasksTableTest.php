<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ActionPlanTasksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ActionPlanTasksTable Test Case
 */
class ActionPlanTasksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ActionPlanTasksTable
     */
    public $ActionPlanTasks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ActionPlanTasks',
        'app.DailyActivities',
        'app.ActionPlans',
        'app.CompleteStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ActionPlanTasks') ? [] : ['className' => ActionPlanTasksTable::class];
        $this->ActionPlanTasks = TableRegistry::getTableLocator()->get('ActionPlanTasks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionPlanTasks);

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
