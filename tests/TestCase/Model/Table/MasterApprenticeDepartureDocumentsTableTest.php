<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterApprenticeDepartureDocumentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterApprenticeDepartureDocumentsTable Test Case
 */
class MasterApprenticeDepartureDocumentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterApprenticeDepartureDocumentsTable
     */
    public $MasterApprenticeDepartureDocuments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterApprenticeDepartureDocuments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterApprenticeDepartureDocuments') ? [] : ['className' => MasterApprenticeDepartureDocumentsTable::class];
        $this->MasterApprenticeDepartureDocuments = TableRegistry::getTableLocator()->get('MasterApprenticeDepartureDocuments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterApprenticeDepartureDocuments);

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
