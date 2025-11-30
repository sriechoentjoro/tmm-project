<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReligionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReligionsTable Test Case
 */
class ReligionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReligionsTable
     */
    public $Religions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Religions',
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
        $config = TableRegistry::getTableLocator()->exists('Religions') ? [] : ['className' => ReligionsTable::class];
        $this->Religions = TableRegistry::getTableLocator()->get('Religions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Religions);

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
