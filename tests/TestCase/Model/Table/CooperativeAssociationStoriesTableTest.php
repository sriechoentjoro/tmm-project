<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CooperativeAssociationStoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CooperativeAssociationStoriesTable Test Case
 */
class CooperativeAssociationStoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CooperativeAssociationStoriesTable
     */
    public $CooperativeAssociationStories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CooperativeAssociationStories',
        'app.CooperativeAssociations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CooperativeAssociationStories') ? [] : ['className' => CooperativeAssociationStoriesTable::class];
        $this->CooperativeAssociationStories = TableRegistry::getTableLocator()->get('CooperativeAssociationStories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CooperativeAssociationStories);

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
