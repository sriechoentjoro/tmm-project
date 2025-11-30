<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterPaymentMethodsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterPaymentMethodsTable Test Case
 */
class MasterPaymentMethodsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterPaymentMethodsTable
     */
    public $MasterPaymentMethods;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterPaymentMethods',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterPaymentMethods') ? [] : ['className' => MasterPaymentMethodsTable::class];
        $this->MasterPaymentMethods = TableRegistry::getTableLocator()->get('MasterPaymentMethods', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterPaymentMethods);

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
