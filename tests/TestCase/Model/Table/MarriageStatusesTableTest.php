<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MarriageStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MarriageStatusesTable Test Case
 */
class MarriageStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MarriageStatusesTable
     */
    public $MarriageStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MarriageStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MarriageStatuses') ? [] : ['className' => MarriageStatusesTable::class];
        $this->MarriageStatuses = TableRegistry::getTableLocator()->get('MarriageStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MarriageStatuses);

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
