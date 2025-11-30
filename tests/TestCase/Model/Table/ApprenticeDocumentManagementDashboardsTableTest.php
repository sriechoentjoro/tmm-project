<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeDocumentManagementDashboardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeDocumentManagementDashboardsTable Test Case
 */
class ApprenticeDocumentManagementDashboardsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeDocumentManagementDashboardsTable
     */
    public $ApprenticeDocumentManagementDashboards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeDocumentManagementDashboards',
        'app.Candidates',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeDocumentManagementDashboards') ? [] : ['className' => ApprenticeDocumentManagementDashboardsTable::class];
        $this->ApprenticeDocumentManagementDashboards = TableRegistry::getTableLocator()->get('ApprenticeDocumentManagementDashboards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeDocumentManagementDashboards);

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
