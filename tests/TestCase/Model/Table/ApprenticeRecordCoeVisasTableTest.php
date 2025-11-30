<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeRecordCoeVisasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeRecordCoeVisasTable Test Case
 */
class ApprenticeRecordCoeVisasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeRecordCoeVisasTable
     */
    public $ApprenticeRecordCoeVisas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeRecordCoeVisas',
        'app.Apprentices',
        'app.MasterCoeTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeRecordCoeVisas') ? [] : ['className' => ApprenticeRecordCoeVisasTable::class];
        $this->ApprenticeRecordCoeVisas = TableRegistry::getTableLocator()->get('ApprenticeRecordCoeVisas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeRecordCoeVisas);

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
