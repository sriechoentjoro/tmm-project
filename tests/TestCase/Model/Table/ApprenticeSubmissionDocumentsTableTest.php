<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeSubmissionDocumentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeSubmissionDocumentsTable Test Case
 */
class ApprenticeSubmissionDocumentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeSubmissionDocumentsTable
     */
    public $ApprenticeSubmissionDocuments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeSubmissionDocuments',
        'app.Apprentices',
        'app.ApprenticeshipSubmissionDocuments',
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
        $config = TableRegistry::getTableLocator()->exists('ApprenticeSubmissionDocuments') ? [] : ['className' => ApprenticeSubmissionDocumentsTable::class];
        $this->ApprenticeSubmissionDocuments = TableRegistry::getTableLocator()->get('ApprenticeSubmissionDocuments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeSubmissionDocuments);

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
