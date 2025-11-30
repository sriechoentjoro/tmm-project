<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeFamiliesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeFamiliesTable Test Case
 */
class TraineeFamiliesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeFamiliesTable
     */
    public $TraineeFamilies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeFamilies',
        'app.Trainees',
        'app.MasterFamilyConnections',
        'app.MasterOccupations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeFamilies') ? [] : ['className' => TraineeFamiliesTable::class];
        $this->TraineeFamilies = TableRegistry::getTableLocator()->get('TraineeFamilies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeFamilies);

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
