<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeExperiencesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeExperiencesTable Test Case
 */
class TraineeExperiencesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeExperiencesTable
     */
    public $TraineeExperiences;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeExperiences',
        'app.Trainees',
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
        $config = TableRegistry::getTableLocator()->exists('TraineeExperiences') ? [] : ['className' => TraineeExperiencesTable::class];
        $this->TraineeExperiences = TableRegistry::getTableLocator()->get('TraineeExperiences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeExperiences);

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
