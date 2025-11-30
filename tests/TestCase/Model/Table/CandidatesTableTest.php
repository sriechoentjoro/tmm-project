<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidatesTable Test Case
 */
class CandidatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidatesTable
     */
    public $Candidates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Candidates',
        'app.ApprenticeOrders',
        'app.VocationalTrainingInstitutions',
        'app.AcceptanceOrganizations',
        'app.MasterGenders',
        'app.MasterReligions',
        'app.MasterMarriageStatuses',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
        'app.MasterKecamatans',
        'app.MasterKelurahans',
        'app.MasterBloodTypes',
        'app.MasterCandidateInterviewResults',
        'app.MasterRejectedReasons',
        'app.CandidateCertifications',
        'app.CandidateCourses',
        'app.CandidateDocuments',
        'app.CandidateEducations',
        'app.CandidateExperiences',
        'app.CandidateFamilies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Candidates') ? [] : ['className' => CandidatesTable::class];
        $this->Candidates = TableRegistry::getTableLocator()->get('Candidates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Candidates);

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
