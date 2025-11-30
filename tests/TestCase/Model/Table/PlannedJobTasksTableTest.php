<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlannedJobTasksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlannedJobTasksTable Test Case
 */
class PlannedJobTasksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlannedJobTasksTable
     */
    public $PlannedJobTasks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PlannedJobTasks',
        'app.PlannedJobs',
        'app.PlannedJobSubs',
        'app.CompleteStatuses',
        'app.BinderPlannedJobTaskResponses',
        'app.PlannedJobTaskPerformers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PlannedJobTasks') ? [] : ['className' => PlannedJobTasksTable::class];
        $this->PlannedJobTasks = TableRegistry::getTableLocator()->get('PlannedJobTasks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlannedJobTasks);

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
