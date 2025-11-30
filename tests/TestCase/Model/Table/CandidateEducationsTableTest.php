<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateEducationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateEducationsTable Test Case
 */
class CandidateEducationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateEducationsTable
     */
    public $CandidateEducations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateEducations',
        'app.Candidates',
        'app.MasterStratas',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateEducations') ? [] : ['className' => CandidateEducationsTable::class];
        $this->CandidateEducations = TableRegistry::getTableLocator()->get('CandidateEducations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateEducations);

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
