<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BreakDownCausesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BreakDownCausesTable Test Case
 */
class BreakDownCausesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BreakDownCausesTable
     */
    public $BreakDownCauses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.BreakDownCauses',
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
        $config = TableRegistry::getTableLocator()->exists('BreakDownCauses') ? [] : ['className' => BreakDownCausesTable::class];
        $this->BreakDownCauses = TableRegistry::getTableLocator()->get('BreakDownCauses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BreakDownCauses);

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
