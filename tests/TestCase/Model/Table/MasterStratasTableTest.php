<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterStratasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterStratasTable Test Case
 */
class MasterStratasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterStratasTable
     */
    public $MasterStratas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterStratas',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterStratas') ? [] : ['className' => MasterStratasTable::class];
        $this->MasterStratas = TableRegistry::getTableLocator()->get('MasterStratas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterStratas);

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
