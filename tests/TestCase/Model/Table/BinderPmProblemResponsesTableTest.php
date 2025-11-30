<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BinderPmProblemResponsesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BinderPmProblemResponsesTable Test Case
 */
class BinderPmProblemResponsesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BinderPmProblemResponsesTable
     */
    public $BinderPmProblemResponses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.BinderPmProblemResponses',
        'app.PreventiveCheckSchedules',
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
        $config = TableRegistry::getTableLocator()->exists('BinderPmProblemResponses') ? [] : ['className' => BinderPmProblemResponsesTable::class];
        $this->BinderPmProblemResponses = TableRegistry::getTableLocator()->get('BinderPmProblemResponses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BinderPmProblemResponses);

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
