<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticesTable Test Case
 */
class ApprenticesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticesTable
     */
    public $Apprentices;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Apprentices',
        'app.Candidates',
        'app.Trainees',
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
        'app.ApprenticeCertifications',
        'app.ApprenticeCourses',
        'app.ApprenticeEducations',
        'app.ApprenticeExperiences',
        'app.ApprenticeFamilies',
        'app.ApprenticeFamilyStories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Apprentices') ? [] : ['className' => ApprenticesTable::class];
        $this->Apprentices = TableRegistry::getTableLocator()->get('Apprentices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Apprentices);

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
