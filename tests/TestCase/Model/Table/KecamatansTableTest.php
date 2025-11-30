<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KecamatansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KecamatansTable Test Case
 */
class KecamatansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KecamatansTable
     */
    public $Kecamatans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Kecamatans',
        'app.Propinsis',
        'app.Kabupatens',
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
        $config = TableRegistry::getTableLocator()->exists('Kecamatans') ? [] : ['className' => KecamatansTable::class];
        $this->Kecamatans = TableRegistry::getTableLocator()->get('Kecamatans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Kecamatans);

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
