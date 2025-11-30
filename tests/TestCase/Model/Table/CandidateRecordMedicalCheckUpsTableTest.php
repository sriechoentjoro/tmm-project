<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateRecordMedicalCheckUpsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateRecordMedicalCheckUpsTable Test Case
 */
class CandidateRecordMedicalCheckUpsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateRecordMedicalCheckUpsTable
     */
    public $CandidateRecordMedicalCheckUps;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateRecordMedicalCheckUps',
        'app.Applicants',
        'app.MedicalCheckUpResults',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateRecordMedicalCheckUps') ? [] : ['className' => CandidateRecordMedicalCheckUpsTable::class];
        $this->CandidateRecordMedicalCheckUps = TableRegistry::getTableLocator()->get('CandidateRecordMedicalCheckUps', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateRecordMedicalCheckUps);

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
