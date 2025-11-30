<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ActionPlansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ActionPlansTable Test Case
 */
class ActionPlansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ActionPlansTable
     */
    public $ActionPlans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ActionPlans',
        'app.DailyActivities',
        'app.CompleteStatuses',
        'app.ActionPlanTasks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ActionPlans') ? [] : ['className' => ActionPlansTable::class];
        $this->ActionPlans = TableRegistry::getTableLocator()->get('ActionPlans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionPlans);

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
