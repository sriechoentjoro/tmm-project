<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterOccupationCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterOccupationCategoriesTable Test Case
 */
class MasterOccupationCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterOccupationCategoriesTable
     */
    public $MasterOccupationCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterOccupationCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterOccupationCategories') ? [] : ['className' => MasterOccupationCategoriesTable::class];
        $this->MasterOccupationCategories = TableRegistry::getTableLocator()->get('MasterOccupationCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterOccupationCategories);

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
