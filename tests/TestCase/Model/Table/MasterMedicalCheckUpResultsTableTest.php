<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterMedicalCheckUpResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterMedicalCheckUpResultsTable Test Case
 */
class MasterMedicalCheckUpResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterMedicalCheckUpResultsTable
     */
    public $MasterMedicalCheckUpResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::getTableLocator()->exists('MasterMedicalCheckUpResults') ? [] : ['className' => MasterMedicalCheckUpResultsTable::class];
        $this->MasterMedicalCheckUpResults = TableRegistry::getTableLocator()->get('MasterMedicalCheckUpResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterMedicalCheckUpResults);

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
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
