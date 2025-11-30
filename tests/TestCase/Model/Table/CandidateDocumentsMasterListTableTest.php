<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateDocumentsMasterListTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateDocumentsMasterListTable Test Case
 */
class CandidateDocumentsMasterListTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateDocumentsMasterListTable
     */
    public $CandidateDocumentsMasterList;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateDocumentsMasterList',
        'app.Categories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateDocumentsMasterList') ? [] : ['className' => CandidateDocumentsMasterListTable::class];
        $this->CandidateDocumentsMasterList = TableRegistry::getTableLocator()->get('CandidateDocumentsMasterList', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateDocumentsMasterList);

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
