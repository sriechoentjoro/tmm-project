<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BanksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BanksTable Test Case
 */
class BanksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BanksTable
     */
    public $Banks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Banks',
        'app.Suppliers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Banks') ? [] : ['className' => BanksTable::class];
        $this->Banks = TableRegistry::getTableLocator()->get('Banks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Banks);

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
}
