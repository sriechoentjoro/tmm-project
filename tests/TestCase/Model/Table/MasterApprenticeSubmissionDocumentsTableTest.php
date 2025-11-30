<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterApprenticeSubmissionDocumentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterApprenticeSubmissionDocumentsTable Test Case
 */
class MasterApprenticeSubmissionDocumentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterApprenticeSubmissionDocumentsTable
     */
    public $MasterApprenticeSubmissionDocuments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterApprenticeSubmissionDocuments',
        'app.MasterApprenticeshipSubmissionDocumentCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterApprenticeSubmissionDocuments') ? [] : ['className' => MasterApprenticeSubmissionDocumentsTable::class];
        $this->MasterApprenticeSubmissionDocuments = TableRegistry::getTableLocator()->get('MasterApprenticeSubmissionDocuments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterApprenticeSubmissionDocuments);

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
