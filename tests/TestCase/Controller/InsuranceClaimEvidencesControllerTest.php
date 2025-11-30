<?php
namespace App\Test\TestCase\Controller;

use App\Controller\InsuranceClaimEvidencesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\InsuranceClaimEvidencesController Test Case
 *
 * @uses \App\Controller\InsuranceClaimEvidencesController
 */
class InsuranceClaimEvidencesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InsuranceClaimEvidences',
        'app.DailyActivities',
        'app.InsuranceClaims',
        'app.InsuranceClaimLosts',
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
     * Test exportPdf method
     *
     * @return void
     */
    public function testExportPdf()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
