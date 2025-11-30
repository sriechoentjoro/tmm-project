<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterTransactionCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterTransactionCategoriesTable Test Case
 */
class MasterTransactionCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterTransactionCategoriesTable
     */
    public $MasterTransactionCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterTransactionCategories',
        'app.TraineeInstallments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterTransactionCategories') ? [] : ['className' => MasterTransactionCategoriesTable::class];
        $this->MasterTransactionCategories = TableRegistry::getTableLocator()->get('MasterTransactionCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterTransactionCategories);

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
