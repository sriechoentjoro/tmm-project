<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TestSmartFormsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TestSmartFormsTable Test Case
 */
class TestSmartFormsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TestSmartFormsTable
     */
    public $TestSmartForms;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TestSmartForms',
        'app.Suppliers',
        'app.Storages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TestSmartForms') ? [] : ['className' => TestSmartFormsTable::class];
        $this->TestSmartForms = TableRegistry::getTableLocator()->get('TestSmartForms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TestSmartForms);

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
}
