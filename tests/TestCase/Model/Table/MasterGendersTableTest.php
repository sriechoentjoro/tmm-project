<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterGendersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterGendersTable Test Case
 */
class MasterGendersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterGendersTable
     */
    public $MasterGenders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterGenders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterGenders') ? [] : ['className' => MasterGendersTable::class];
        $this->MasterGenders = TableRegistry::getTableLocator()->get('MasterGenders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterGenders);

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
