<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineesTable Test Case
 */
class TraineesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineesTable
     */
    public $Trainees;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Trainees',
        'app.Candidates',
        'app.ApprenticeOrders',
        'app.Trainings',
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
        'app.MasterInterviewResults',
        'app.MasterRejectedReasons',
        'app.TraineeCertifications',
        'app.TraineeCourses',
        'app.TraineeEducations',
        'app.TraineeExperiences',
        'app.TraineeFamilies',
        'app.TraineeFamilyStories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Trainees') ? [] : ['className' => TraineesTable::class];
        $this->Trainees = TableRegistry::getTableLocator()->get('Trainees', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trainees);

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
