<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GroupSubsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GroupSubsTable Test Case
 */
class GroupSubsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GroupSubsTable
     */
    public $GroupSubs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.GroupSubs',
        'app.Departments',
        'app.Sections',
        'app.Groups',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('GroupSubs') ? [] : ['className' => GroupSubsTable::class];
        $this->GroupSubs = TableRegistry::getTableLocator()->get('GroupSubs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupSubs);

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
