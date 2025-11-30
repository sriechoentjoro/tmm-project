<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KelurahansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KelurahansTable Test Case
 */
class KelurahansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KelurahansTable
     */
    public $Kelurahans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Kelurahans',
        'app.Propinsis',
        'app.Kabupatens',
        'app.Kecamatans',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Kelurahans') ? [] : ['className' => KelurahansTable::class];
        $this->Kelurahans = TableRegistry::getTableLocator()->get('Kelurahans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Kelurahans);

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
