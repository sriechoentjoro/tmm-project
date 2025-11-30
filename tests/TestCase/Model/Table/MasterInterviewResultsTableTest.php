<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterInterviewResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterInterviewResultsTable Test Case
 */
class MasterInterviewResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterInterviewResultsTable
     */
    public $MasterInterviewResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterInterviewResults',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterInterviewResults') ? [] : ['className' => MasterInterviewResultsTable::class];
        $this->MasterInterviewResults = TableRegistry::getTableLocator()->get('MasterInterviewResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterInterviewResults);

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
