<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SpStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SpStatusesTable Test Case
 */
class SpStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SpStatusesTable
     */
    public $SpStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.SpStatuses',
        'app.DailyActivities',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SpStatuses') ? [] : ['className' => SpStatusesTable::class];
        $this->SpStatuses = TableRegistry::getTableLocator()->get('SpStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SpStatuses);

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
