<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DailyActivitiesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DailyActivitiesTable Test Case
 */
class DailyActivitiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DailyActivitiesTable
     */
    public $DailyActivities;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DailyActivities',
        'app.ActionStatuses',
        'app.LineSections',
        'app.LineProcesses',
        'app.LineGroups',
        'app.Lines',
        'app.LineFunctions',
        'app.LineUnits',
        'app.LineParts',
        'app.JobCategories',
        'app.SpStatuses',
        'app.Shifts',
        'app.BreakDownCauses',
        'app.JobStatuses',
        'app.ActionImages',
        'app.ActionPerformers',
        'app.ActionPlanTasks',
        'app.ActionPlans',
        'app.Actions',
        'app.BinderPlannedJobTaskResponses',
        'app.BinderPmProblemResponses',
        'app.CorrectiveActionFiles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DailyActivities') ? [] : ['className' => DailyActivitiesTable::class];
        $this->DailyActivities = TableRegistry::getTableLocator()->get('DailyActivities', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DailyActivities);

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
