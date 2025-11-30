<?php
namespace App\Test\TestCase\Controller;

use App\Controller\DailyActivitiesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\DailyActivitiesController Test Case
 *
 * @uses \App\Controller\DailyActivitiesController
 */
class DailyActivitiesControllerTest extends TestCase
{
    use IntegrationTestTrait;

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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
