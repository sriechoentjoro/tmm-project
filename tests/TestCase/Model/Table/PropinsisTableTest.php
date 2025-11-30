<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PropinsisTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PropinsisTable Test Case
 */
class PropinsisTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PropinsisTable
     */
    public $Propinsis;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Propinsis',
        'app.Kabupatens',
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
        $config = TableRegistry::getTableLocator()->exists('Propinsis') ? [] : ['className' => PropinsisTable::class];
        $this->Propinsis = TableRegistry::getTableLocator()->get('Propinsis', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Propinsis);

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
