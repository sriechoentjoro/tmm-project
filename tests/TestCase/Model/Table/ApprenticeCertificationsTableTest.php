<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeCertificationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeCertificationsTable Test Case
 */
class ApprenticeCertificationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeCertificationsTable
     */
    public $ApprenticeCertifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeCertifications',
        'app.Apprentices',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeCertifications') ? [] : ['className' => ApprenticeCertificationsTable::class];
        $this->ApprenticeCertifications = TableRegistry::getTableLocator()->get('ApprenticeCertifications', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeCertifications);

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
