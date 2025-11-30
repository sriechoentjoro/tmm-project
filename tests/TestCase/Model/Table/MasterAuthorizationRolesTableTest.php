<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterAuthorizationRolesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterAuthorizationRolesTable Test Case
 */
class MasterAuthorizationRolesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterAuthorizationRolesTable
     */
    public $MasterAuthorizationRoles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterAuthorizationRoles',
        'app.Authorizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterAuthorizationRoles') ? [] : ['className' => MasterAuthorizationRolesTable::class];
        $this->MasterAuthorizationRoles = TableRegistry::getTableLocator()->get('MasterAuthorizationRoles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterAuthorizationRoles);

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
