<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ActionImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ActionImagesTable Test Case
 */
class ActionImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ActionImagesTable
     */
    public $ActionImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ActionImages',
        'app.DailyActivities',
        'app.Actions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ActionImages') ? [] : ['className' => ActionImagesTable::class];
        $this->ActionImages = TableRegistry::getTableLocator()->get('ActionImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionImages);

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
