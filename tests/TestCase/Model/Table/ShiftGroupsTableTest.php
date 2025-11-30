<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShiftGroupsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShiftGroupsTable Test Case
 */
class ShiftGroupsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ShiftGroupsTable
     */
    public $ShiftGroups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ShiftGroups',
        'app.Personnels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ShiftGroups') ? [] : ['className' => ShiftGroupsTable::class];
        $this->ShiftGroups = TableRegistry::getTableLocator()->get('ShiftGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ShiftGroups);

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
