<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InsuranceClaimClassesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InsuranceClaimClassesTable Test Case
 */
class InsuranceClaimClassesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\InsuranceClaimClassesTable
     */
    public $InsuranceClaimClasses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InsuranceClaimClasses',
        'app.InsuranceClaims',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('InsuranceClaimClasses') ? [] : ['className' => InsuranceClaimClassesTable::class];
        $this->InsuranceClaimClasses = TableRegistry::getTableLocator()->get('InsuranceClaimClasses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InsuranceClaimClasses);

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
