<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeFamilyStoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeFamilyStoriesTable Test Case
 */
class TraineeFamilyStoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeFamilyStoriesTable
     */
    public $TraineeFamilyStories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeFamilyStories',
        'app.Trainees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeFamilyStories') ? [] : ['className' => TraineeFamilyStoriesTable::class];
        $this->TraineeFamilyStories = TableRegistry::getTableLocator()->get('TraineeFamilyStories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeFamilyStories);

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
