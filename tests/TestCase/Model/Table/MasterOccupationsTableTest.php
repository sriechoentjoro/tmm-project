<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterOccupationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterOccupationsTable Test Case
 */
class MasterOccupationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterOccupationsTable
     */
    public $MasterOccupations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterOccupations',
        'app.OccupationCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterOccupations') ? [] : ['className' => MasterOccupationsTable::class];
        $this->MasterOccupations = TableRegistry::getTableLocator()->get('MasterOccupations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterOccupations);

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
