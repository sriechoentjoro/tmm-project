<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterReligionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterReligionsTable Test Case
 */
class MasterReligionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterReligionsTable
     */
    public $MasterReligions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterReligions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterReligions') ? [] : ['className' => MasterReligionsTable::class];
        $this->MasterReligions = TableRegistry::getTableLocator()->get('MasterReligions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterReligions);

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
