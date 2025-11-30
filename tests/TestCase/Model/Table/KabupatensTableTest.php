<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KabupatensTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KabupatensTable Test Case
 */
class KabupatensTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KabupatensTable
     */
    public $Kabupatens;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Kabupatens',
        'app.Propinsis',
        'app.Kecamatans',
        'app.Kelurahans',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Kabupatens') ? [] : ['className' => KabupatensTable::class];
        $this->Kabupatens = TableRegistry::getTableLocator()->get('Kabupatens', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Kabupatens);

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
