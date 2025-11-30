<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlannedJobsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlannedJobsTable Test Case
 */
class PlannedJobsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlannedJobsTable
     */
    public $PlannedJobs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PlannedJobs',
        'app.LineSections',
        'app.LineProcesses',
        'app.LineGroups',
        'app.Lines',
        'app.LineFunctions',
        'app.LineUnits',
        'app.LineParts',
        'app.PlannedJobOwners',
        'app.JobCategories',
        'app.Priorities',
        'app.CompleteStatuses',
        'app.BinderPlannedJobTaskResponses',
        'app.PlannedJobPreparations',
        'app.PlannedJobSubs',
        'app.PlannedJobTaskPerformers',
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
        $config = TableRegistry::getTableLocator()->exists('PlannedJobs') ? [] : ['className' => PlannedJobsTable::class];
        $this->PlannedJobs = TableRegistry::getTableLocator()->get('PlannedJobs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlannedJobs);

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
