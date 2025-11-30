<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterKelurahansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterKelurahansTable Test Case
 */
class MasterKelurahansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterKelurahansTable
     */
    public $MasterKelurahans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterKelurahans',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
        'app.MasterKecamatans',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterKelurahans') ? [] : ['className' => MasterKelurahansTable::class];
        $this->MasterKelurahans = TableRegistry::getTableLocator()->get('MasterKelurahans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterKelurahans);

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
