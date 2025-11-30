<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterPropinsisTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterPropinsisTable Test Case
 */
class MasterPropinsisTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterPropinsisTable
     */
    public $MasterPropinsis;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterPropinsis',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterPropinsis') ? [] : ['className' => MasterPropinsisTable::class];
        $this->MasterPropinsis = TableRegistry::getTableLocator()->get('MasterPropinsis', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterPropinsis);

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
