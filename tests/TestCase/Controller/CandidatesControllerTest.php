<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CandidatesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CandidatesController Test Case
 *
 * @uses \App\Controller\CandidatesController
 */
class CandidatesControllerTest extends TestCase
{
    use IntegrationTestTrait;

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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test preview method
     *
     * @return void
     */
    public function testPreview()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test exportCsv method
     *
     * @return void
     */
    public function testExportCsv()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test exportExcel method
     *
     * @return void
     */
    public function testExportExcel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test printReport method
     *
     * @return void
     */
    public function testPrintReport()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test exportPdf method
     *
     * @return void
     */
    public function testExportPdf()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test doExportCsv method
     *
     * @return void
     */
    public function testDoExportCsv()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test doExportExcel method
     *
     * @return void
     */
    public function testDoExportExcel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test doExportPrint method
     *
     * @return void
     */
    public function testDoExportPrint()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
