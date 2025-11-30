<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CandidateCertificationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CandidateCertificationsTable Test Case
 */
class CandidateCertificationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CandidateCertificationsTable
     */
    public $CandidateCertifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CandidateCertifications',
        'app.Candidates',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CandidateCertifications') ? [] : ['className' => CandidateCertificationsTable::class];
        $this->CandidateCertifications = TableRegistry::getTableLocator()->get('CandidateCertifications', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CandidateCertifications);

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
