<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApprenticeExperiencesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApprenticeExperiencesTable Test Case
 */
class ApprenticeExperiencesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApprenticeExperiencesTable
     */
    public $ApprenticeExperiences;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ApprenticeExperiences',
        'app.Apprentices',
        'app.MasterEmployeeStatuses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ApprenticeExperiences') ? [] : ['className' => ApprenticeExperiencesTable::class];
        $this->ApprenticeExperiences = TableRegistry::getTableLocator()->get('ApprenticeExperiences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprenticeExperiences);

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
