<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeOrdersTable Test Case
 */
class ApprenticeOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeOrdersTable
     */
    public $ApprenticeOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeOrders',
        'app.CooperativeAssociations',
        'app.AcceptanceOrganizations',
        'app.MasterJobCategories',
        'app.Apprentices',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeOrders') ? [] : ['className' => ApprenticeOrdersTable::class];
        $this->ApprenticeOrders = TableRegistry::getTableLocator()->get('ApprenticeOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeOrders);

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
     * Test beforeSave method
     *
     * @return void
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test generateTitle method
     *
     * @return void
     */
    public function testGenerateTitle()
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
