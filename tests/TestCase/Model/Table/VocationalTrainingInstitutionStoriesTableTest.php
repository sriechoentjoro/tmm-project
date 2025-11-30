<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VocationalTrainingInstitutionStoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VocationalTrainingInstitutionStoriesTable Test Case
 */
class VocationalTrainingInstitutionStoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VocationalTrainingInstitutionStoriesTable
     */
    public $VocationalTrainingInstitutionStories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.VocationalTrainingInstitutionStories',
        'app.VocationalTrainingInstitutions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('VocationalTrainingInstitutionStories') ? [] : ['className' => VocationalTrainingInstitutionStoriesTable::class];
        $this->VocationalTrainingInstitutionStories = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutionStories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->VocationalTrainingInstitutionStories);

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
