<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterRejectedReasonsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterRejectedReasonsTable Test Case
 */
class MasterRejectedReasonsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterRejectedReasonsTable
     */
    public $MasterRejectedReasons;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterRejectedReasons',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterRejectedReasons') ? [] : ['className' => MasterRejectedReasonsTable::class];
        $this->MasterRejectedReasons = TableRegistry::getTableLocator()->get('MasterRejectedReasons', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterRejectedReasons);

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
