<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BinderPlannedJobTaskResponsesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BinderPlannedJobTaskResponsesTable Test Case
 */
class BinderPlannedJobTaskResponsesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BinderPlannedJobTaskResponsesTable
     */
    public $BinderPlannedJobTaskResponses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.BinderPlannedJobTaskResponses',
        'app.DailyActivities',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('BinderPlannedJobTaskResponses') ? [] : ['className' => BinderPlannedJobTaskResponsesTable::class];
        $this->BinderPlannedJobTaskResponses = TableRegistry::getTableLocator()->get('BinderPlannedJobTaskResponses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BinderPlannedJobTaskResponses);

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
