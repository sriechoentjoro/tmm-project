<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterKecamatansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterKecamatansTable Test Case
 */
class MasterKecamatansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterKecamatansTable
     */
    public $MasterKecamatans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterKecamatans',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterKecamatans') ? [] : ['className' => MasterKecamatansTable::class];
        $this->MasterKecamatans = TableRegistry::getTableLocator()->get('MasterKecamatans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterKecamatans);

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
