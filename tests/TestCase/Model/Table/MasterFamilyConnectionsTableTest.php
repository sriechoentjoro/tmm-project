<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterFamilyConnectionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterFamilyConnectionsTable Test Case
 */
class MasterFamilyConnectionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterFamilyConnectionsTable
     */
    public $MasterFamilyConnections;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterFamilyConnections',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterFamilyConnections') ? [] : ['className' => MasterFamilyConnectionsTable::class];
        $this->MasterFamilyConnections = TableRegistry::getTableLocator()->get('MasterFamilyConnections', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterFamilyConnections);

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
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
