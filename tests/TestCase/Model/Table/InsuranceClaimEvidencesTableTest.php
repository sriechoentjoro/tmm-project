<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InsuranceClaimEvidencesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InsuranceClaimEvidencesTable Test Case
 */
class InsuranceClaimEvidencesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\InsuranceClaimEvidencesTable
     */
    public $InsuranceClaimEvidences;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InsuranceClaimEvidences',
        'app.DailyActivities',
        'app.InsuranceClaims',
        'app.InsuranceClaimLosts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('InsuranceClaimEvidences') ? [] : ['className' => InsuranceClaimEvidencesTable::class];
        $this->InsuranceClaimEvidences = TableRegistry::getTableLocator()->get('InsuranceClaimEvidences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InsuranceClaimEvidences);

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
