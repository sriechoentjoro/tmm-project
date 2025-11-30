<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\InsuranceClaimPhotosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\InsuranceClaimPhotosTable Test Case
 */
class InsuranceClaimPhotosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\InsuranceClaimPhotosTable
     */
    public $InsuranceClaimPhotos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.InsuranceClaimPhotos',
        'app.DailyActivities',
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
        $config = TableRegistry::getTableLocator()->exists('InsuranceClaimPhotos') ? [] : ['className' => InsuranceClaimPhotosTable::class];
        $this->InsuranceClaimPhotos = TableRegistry::getTableLocator()->get('InsuranceClaimPhotos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->InsuranceClaimPhotos);

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
