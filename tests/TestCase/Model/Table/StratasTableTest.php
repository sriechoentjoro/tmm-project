<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StratasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StratasTable Test Case
 */
class StratasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StratasTable
     */
    public $Stratas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Stratas',
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
        $config = TableRegistry::getTableLocator()->exists('Stratas') ? [] : ['className' => StratasTable::class];
        $this->Stratas = TableRegistry::getTableLocator()->get('Stratas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Stratas);

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
