<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlannedJobTaskPerformersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlannedJobTaskPerformersTable Test Case
 */
class PlannedJobTaskPerformersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlannedJobTaskPerformersTable
     */
    public $PlannedJobTaskPerformers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PlannedJobTaskPerformers',
        'app.PlannedJobs',
        'app.PlannedJobTasks',
        'app.MaintenancePersonnels',
        'app.ProductionPersonnels',
        'app.Vendors',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PlannedJobTaskPerformers') ? [] : ['className' => PlannedJobTaskPerformersTable::class];
        $this->PlannedJobTaskPerformers = TableRegistry::getTableLocator()->get('PlannedJobTaskPerformers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlannedJobTaskPerformers);

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
