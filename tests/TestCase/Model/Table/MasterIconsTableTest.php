<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterIconsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterIconsTable Test Case
 */
class MasterIconsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterIconsTable
     */
    public $MasterIcons;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterIcons',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterIcons') ? [] : ['className' => MasterIconsTable::class];
        $this->MasterIcons = TableRegistry::getTableLocator()->get('MasterIcons', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterIcons);

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
