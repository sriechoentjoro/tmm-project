<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeCertificationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeCertificationsTable Test Case
 */
class TraineeCertificationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeCertificationsTable
     */
    public $TraineeCertifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeCertifications',
        'app.Trainees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeCertifications') ? [] : ['className' => TraineeCertificationsTable::class];
        $this->TraineeCertifications = TableRegistry::getTableLocator()->get('TraineeCertifications', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeCertifications);

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
