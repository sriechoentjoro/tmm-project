<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterMarriageStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterMarriageStatusesTable Test Case
 */
class MasterMarriageStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterMarriageStatusesTable
     */
    public $MasterMarriageStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterMarriageStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterMarriageStatuses') ? [] : ['className' => MasterMarriageStatusesTable::class];
        $this->MasterMarriageStatuses = TableRegistry::getTableLocator()->get('MasterMarriageStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterMarriageStatuses);

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
