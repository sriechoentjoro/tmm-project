<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateSubmissionDocumentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateSubmissionDocumentsTable Test Case
 */
class CandidateSubmissionDocumentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateSubmissionDocumentsTable
     */
    public $CandidateSubmissionDocuments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateSubmissionDocuments',
        'app.Applicants',
        'app.Documents',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateSubmissionDocuments') ? [] : ['className' => CandidateSubmissionDocumentsTable::class];
        $this->CandidateSubmissionDocuments = TableRegistry::getTableLocator()->get('CandidateSubmissionDocuments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateSubmissionDocuments);

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
