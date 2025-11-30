<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterCandidateInterviewTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterCandidateInterviewTypesTable Test Case
 */
class MasterCandidateInterviewTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterCandidateInterviewTypesTable
     */
    public $MasterCandidateInterviewTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterCandidateInterviewTypes',
        'app.CandidateRecordInterviews',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterCandidateInterviewTypes') ? [] : ['className' => MasterCandidateInterviewTypesTable::class];
        $this->MasterCandidateInterviewTypes = TableRegistry::getTableLocator()->get('MasterCandidateInterviewTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterCandidateInterviewTypes);

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
