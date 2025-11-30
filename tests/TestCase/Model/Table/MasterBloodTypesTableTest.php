<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterBloodTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterBloodTypesTable Test Case
 */
class MasterBloodTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterBloodTypesTable
     */
    public $MasterBloodTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterBloodTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterBloodTypes') ? [] : ['className' => MasterBloodTypesTable::class];
        $this->MasterBloodTypes = TableRegistry::getTableLocator()->get('MasterBloodTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterBloodTypes);

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
