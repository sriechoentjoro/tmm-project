<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterCandidateInterviewResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterCandidateInterviewResultsTable Test Case
 */
class MasterCandidateInterviewResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterCandidateInterviewResultsTable
     */
    public $MasterCandidateInterviewResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterCandidateInterviewResults',
        'app.CandidateRecordInterviews',
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
        $config = TableRegistry::getTableLocator()->exists('MasterCandidateInterviewResults') ? [] : ['className' => MasterCandidateInterviewResultsTable::class];
        $this->MasterCandidateInterviewResults = TableRegistry::getTableLocator()->get('MasterCandidateInterviewResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterCandidateInterviewResults);

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
