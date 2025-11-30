<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprovalMatrixTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprovalMatrixTable Test Case
 */
class ApprovalMatrixTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprovalMatrixTable
     */
    public $ApprovalMatrix;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprovalMatrix',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprovalMatrix') ? [] : ['className' => ApprovalMatrixTable::class];
        $this->ApprovalMatrix = TableRegistry::getTableLocator()->get('ApprovalMatrix', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprovalMatrix);

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
