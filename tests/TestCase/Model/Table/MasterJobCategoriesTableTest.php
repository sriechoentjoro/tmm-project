<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterJobCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterJobCategoriesTable Test Case
 */
class MasterJobCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterJobCategoriesTable
     */
    public $MasterJobCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterJobCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterJobCategories') ? [] : ['className' => MasterJobCategoriesTable::class];
        $this->MasterJobCategories = TableRegistry::getTableLocator()->get('MasterJobCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterJobCategories);

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
