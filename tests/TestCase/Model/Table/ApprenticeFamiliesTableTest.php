<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeFamiliesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeFamiliesTable Test Case
 */
class ApprenticeFamiliesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeFamiliesTable
     */
    public $ApprenticeFamilies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeFamilies',
        'app.Apprentices',
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
        $config = TableRegistry::getTableLocator()->exists('ApprenticeFamilies') ? [] : ['className' => ApprenticeFamiliesTable::class];
        $this->ApprenticeFamilies = TableRegistry::getTableLocator()->get('ApprenticeFamilies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeFamilies);

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
