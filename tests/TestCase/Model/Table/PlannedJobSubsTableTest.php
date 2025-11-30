<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlannedJobSubsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlannedJobSubsTable Test Case
 */
class PlannedJobSubsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlannedJobSubsTable
     */
    public $PlannedJobSubs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PlannedJobSubs',
        'app.PlannedJobs',
        'app.BinderPlannedJobTaskResponses',
        'app.PlannedJobPreparations',
        'app.PlannedJobTasks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PlannedJobSubs') ? [] : ['className' => PlannedJobSubsTable::class];
        $this->PlannedJobSubs = TableRegistry::getTableLocator()->get('PlannedJobSubs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlannedJobSubs);

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
