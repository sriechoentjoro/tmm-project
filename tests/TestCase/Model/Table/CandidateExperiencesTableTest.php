<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateExperiencesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateExperiencesTable Test Case
 */
class CandidateExperiencesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateExperiencesTable
     */
    public $CandidateExperiences;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateExperiences',
        'app.Candidates',
        'app.MasterEmployeeStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateExperiences') ? [] : ['className' => CandidateExperiencesTable::class];
        $this->CandidateExperiences = TableRegistry::getTableLocator()->get('CandidateExperiences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateExperiences);

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
