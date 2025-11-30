<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TmsmsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TmsmsTable Test Case
 */
class TmsmsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TmsmsTable
     */
    public $Tmsms;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tmsms',
        'app.TmsmFiles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tmsms') ? [] : ['className' => TmsmsTable::class];
        $this->Tmsms = TableRegistry::getTableLocator()->get('Tmsms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tmsms);

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
