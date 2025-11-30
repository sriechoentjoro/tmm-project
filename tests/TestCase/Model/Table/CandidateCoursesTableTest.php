<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateCoursesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateCoursesTable Test Case
 */
class CandidateCoursesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateCoursesTable
     */
    public $CandidateCourses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateCourses',
        'app.Candidates',
        'app.VocationalTrainingInstitutions',
        'app.MasterCourseMajors',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateCourses') ? [] : ['className' => CandidateCoursesTable::class];
        $this->CandidateCourses = TableRegistry::getTableLocator()->get('CandidateCourses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateCourses);

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
