<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DailyActivityImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DailyActivityImagesTable Test Case
 */
class DailyActivityImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DailyActivityImagesTable
     */
    public $DailyActivityImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DailyActivityImages',
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
        $config = TableRegistry::getTableLocator()->exists('DailyActivityImages') ? [] : ['className' => DailyActivityImagesTable::class];
        $this->DailyActivityImages = TableRegistry::getTableLocator()->get('DailyActivityImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DailyActivityImages);

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
