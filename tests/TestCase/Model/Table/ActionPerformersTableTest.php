<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ActionPerformersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ActionPerformersTable Test Case
 */
class ActionPerformersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ActionPerformersTable
     */
    public $ActionPerformers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ActionPerformers',
        'app.DailyActivities',
        'app.Actions',
        'app.Personnels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ActionPerformers') ? [] : ['className' => ActionPerformersTable::class];
        $this->ActionPerformers = TableRegistry::getTableLocator()->get('ActionPerformers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionPerformers);

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
