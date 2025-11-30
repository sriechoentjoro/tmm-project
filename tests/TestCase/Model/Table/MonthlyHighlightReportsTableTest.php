<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MonthlyHighlightReportsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MonthlyHighlightReportsTable Test Case
 */
class MonthlyHighlightReportsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MonthlyHighlightReportsTable
     */
    public $MonthlyHighlightReports;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MonthlyHighlightReports',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MonthlyHighlightReports') ? [] : ['className' => MonthlyHighlightReportsTable::class];
        $this->MonthlyHighlightReports = TableRegistry::getTableLocator()->get('MonthlyHighlightReports', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MonthlyHighlightReports);

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
