<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterDocumentSubmissionStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterDocumentSubmissionStatusesTable Test Case
 */
class MasterDocumentSubmissionStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterDocumentSubmissionStatusesTable
     */
    public $MasterDocumentSubmissionStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterDocumentSubmissionStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterDocumentSubmissionStatuses') ? [] : ['className' => MasterDocumentSubmissionStatusesTable::class];
        $this->MasterDocumentSubmissionStatuses = TableRegistry::getTableLocator()->get('MasterDocumentSubmissionStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterDocumentSubmissionStatuses);

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
