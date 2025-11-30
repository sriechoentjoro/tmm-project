<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InsuranceClaimsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InsuranceClaimsTable Test Case
 */
class InsuranceClaimsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\InsuranceClaimsTable
     */
    public $InsuranceClaims;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InsuranceClaims',
        'app.DailyActivities',
        'app.InsuranceClaimClasses',
        'app.InsuranceClaimEvidences',
        'app.InsuranceClaimLosts',
        'app.InsuranceClaimPhotos',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('InsuranceClaims') ? [] : ['className' => InsuranceClaimsTable::class];
        $this->InsuranceClaims = TableRegistry::getTableLocator()->get('InsuranceClaims', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InsuranceClaims);

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
