<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterTrainingCompetenciesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterTrainingCompetenciesTable Test Case
 */
class MasterTrainingCompetenciesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterTrainingCompetenciesTable
     */
    public $MasterTrainingCompetencies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterTrainingCompetencies',
        'app.TraineeScoreAverages',
        'app.TraineeTrainingTestScores',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterTrainingCompetencies') ? [] : ['className' => MasterTrainingCompetenciesTable::class];
        $this->MasterTrainingCompetencies = TableRegistry::getTableLocator()->get('MasterTrainingCompetencies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterTrainingCompetencies);

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
