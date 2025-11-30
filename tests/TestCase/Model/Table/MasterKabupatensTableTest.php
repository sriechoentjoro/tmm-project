<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterKabupatensTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterKabupatensTable Test Case
 */
class MasterKabupatensTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterKabupatensTable
     */
    public $MasterKabupatens;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterKabupatens',
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
        $config = TableRegistry::getTableLocator()->exists('MasterKabupatens') ? [] : ['className' => MasterKabupatensTable::class];
        $this->MasterKabupatens = TableRegistry::getTableLocator()->get('MasterKabupatens', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterKabupatens);

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
