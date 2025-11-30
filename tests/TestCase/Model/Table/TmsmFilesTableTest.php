<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TmsmFilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TmsmFilesTable Test Case
 */
class TmsmFilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TmsmFilesTable
     */
    public $TmsmFiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TmsmFiles',
        'app.Tmsms',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TmsmFiles') ? [] : ['className' => TmsmFilesTable::class];
        $this->TmsmFiles = TableRegistry::getTableLocator()->get('TmsmFiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TmsmFiles);

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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
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
