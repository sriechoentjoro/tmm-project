<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterApprenticeSubmissionDocumentCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterApprenticeSubmissionDocumentCategoriesTable Test Case
 */
class MasterApprenticeSubmissionDocumentCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterApprenticeSubmissionDocumentCategoriesTable
     */
    public $MasterApprenticeSubmissionDocumentCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterApprenticeSubmissionDocumentCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterApprenticeSubmissionDocumentCategories') ? [] : ['className' => MasterApprenticeSubmissionDocumentCategoriesTable::class];
        $this->MasterApprenticeSubmissionDocumentCategories = TableRegistry::getTableLocator()->get('MasterApprenticeSubmissionDocumentCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterApprenticeSubmissionDocumentCategories);

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
