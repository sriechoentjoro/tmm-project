<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LesorCompaniesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LesorCompaniesTable Test Case
 */
class LesorCompaniesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LesorCompaniesTable
     */
    public $LesorCompanies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.LesorCompanies',
        'app.Vehicles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('LesorCompanies') ? [] : ['className' => LesorCompaniesTable::class];
        $this->LesorCompanies = TableRegistry::getTableLocator()->get('LesorCompanies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LesorCompanies);

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
