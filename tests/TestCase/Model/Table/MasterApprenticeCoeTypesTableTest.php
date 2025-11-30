<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterApprenticeCoeTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterApprenticeCoeTypesTable Test Case
 */
class MasterApprenticeCoeTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterApprenticeCoeTypesTable
     */
    public $MasterApprenticeCoeTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterApprenticeCoeTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterApprenticeCoeTypes') ? [] : ['className' => MasterApprenticeCoeTypesTable::class];
        $this->MasterApprenticeCoeTypes = TableRegistry::getTableLocator()->get('MasterApprenticeCoeTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterApprenticeCoeTypes);

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
