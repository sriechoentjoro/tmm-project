<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeRecordMedicalCheckUpsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeRecordMedicalCheckUpsTable Test Case
 */
class ApprenticeRecordMedicalCheckUpsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeRecordMedicalCheckUpsTable
     */
    public $ApprenticeRecordMedicalCheckUps;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeRecordMedicalCheckUps',
        'app.Apprentices',
        'app.MasterMedicalCheckUpResults',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeRecordMedicalCheckUps') ? [] : ['className' => ApprenticeRecordMedicalCheckUpsTable::class];
        $this->ApprenticeRecordMedicalCheckUps = TableRegistry::getTableLocator()->get('ApprenticeRecordMedicalCheckUps', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeRecordMedicalCheckUps);

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
