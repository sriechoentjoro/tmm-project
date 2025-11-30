<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateFamiliesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateFamiliesTable Test Case
 */
class CandidateFamiliesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateFamiliesTable
     */
    public $CandidateFamilies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateFamilies',
        'app.Candidates',
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
        $config = TableRegistry::getTableLocator()->exists('CandidateFamilies') ? [] : ['className' => CandidateFamiliesTable::class];
        $this->CandidateFamilies = TableRegistry::getTableLocator()->get('CandidateFamilies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateFamilies);

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
