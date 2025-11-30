<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateRecordInterviewsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateRecordInterviewsTable Test Case
 */
class CandidateRecordInterviewsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateRecordInterviewsTable
     */
    public $CandidateRecordInterviews;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateRecordInterviews',
        'app.Applicants',
        'app.MasterCandidateInterviewTypes',
        'app.MasterCandidateInterviewResults',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateRecordInterviews') ? [] : ['className' => CandidateRecordInterviewsTable::class];
        $this->CandidateRecordInterviews = TableRegistry::getTableLocator()->get('CandidateRecordInterviews', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateRecordInterviews);

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
