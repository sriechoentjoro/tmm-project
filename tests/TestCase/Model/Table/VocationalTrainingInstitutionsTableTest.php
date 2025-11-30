<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VocationalTrainingInstitutionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VocationalTrainingInstitutionsTable Test Case
 */
class VocationalTrainingInstitutionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VocationalTrainingInstitutionsTable
     */
    public $VocationalTrainingInstitutions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.VocationalTrainingInstitutions',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
        'app.MasterKecamatans',
        'app.MasterKelurahans',
        'app.VocationalTrainingInstitutionStories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('VocationalTrainingInstitutions') ? [] : ['className' => VocationalTrainingInstitutionsTable::class];
        $this->VocationalTrainingInstitutions = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->VocationalTrainingInstitutions);

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
