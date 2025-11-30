<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateDocumentCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateDocumentCategoriesTable Test Case
 */
class CandidateDocumentCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateDocumentCategoriesTable
     */
    public $CandidateDocumentCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateDocumentCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateDocumentCategories') ? [] : ['className' => CandidateDocumentCategoriesTable::class];
        $this->CandidateDocumentCategories = TableRegistry::getTableLocator()->get('CandidateDocumentCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateDocumentCategories);

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
