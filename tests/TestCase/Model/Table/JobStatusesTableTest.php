<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JobStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JobStatusesTable Test Case
 */
class JobStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JobStatusesTable
     */
    public $JobStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.JobStatuses',
        'app.Actions',
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
        $config = TableRegistry::getTableLocator()->exists('JobStatuses') ? [] : ['className' => JobStatusesTable::class];
        $this->JobStatuses = TableRegistry::getTableLocator()->get('JobStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JobStatuses);

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
