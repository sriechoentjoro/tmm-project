<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterEmploymentPositionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterEmploymentPositionsTable Test Case
 */
class MasterEmploymentPositionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterEmploymentPositionsTable
     */
    public $MasterEmploymentPositions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterEmploymentPositions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterEmploymentPositions') ? [] : ['className' => MasterEmploymentPositionsTable::class];
        $this->MasterEmploymentPositions = TableRegistry::getTableLocator()->get('MasterEmploymentPositions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterEmploymentPositions);

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
