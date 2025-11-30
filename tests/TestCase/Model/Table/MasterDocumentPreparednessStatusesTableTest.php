<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterDocumentPreparednessStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterDocumentPreparednessStatusesTable Test Case
 */
class MasterDocumentPreparednessStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterDocumentPreparednessStatusesTable
     */
    public $MasterDocumentPreparednessStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterDocumentPreparednessStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterDocumentPreparednessStatuses') ? [] : ['className' => MasterDocumentPreparednessStatusesTable::class];
        $this->MasterDocumentPreparednessStatuses = TableRegistry::getTableLocator()->get('MasterDocumentPreparednessStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterDocumentPreparednessStatuses);

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
