<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SafetyCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SafetyCardsTable Test Case
 */
class SafetyCardsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SafetyCardsTable
     */
    public $SafetyCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.SafetyCards',
        'app.LineSections',
        'app.LineProcesses',
        'app.Lines',
        'app.LineFunctions',
        'app.LineUnits',
        'app.LineParts',
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
        $config = TableRegistry::getTableLocator()->exists('SafetyCards') ? [] : ['className' => SafetyCardsTable::class];
        $this->SafetyCards = TableRegistry::getTableLocator()->get('SafetyCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SafetyCards);

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
