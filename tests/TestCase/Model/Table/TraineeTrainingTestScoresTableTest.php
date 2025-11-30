<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeTrainingTestScoresTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeTrainingTestScoresTable Test Case
 */
class TraineeTrainingTestScoresTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeTrainingTestScoresTable
     */
    public $TraineeTrainingTestScores;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeTrainingTestScores',
        'app.Trainees',
        'app.MasterTrainingCompetencies',
        'app.MasterTrainingTestScoreGrades',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeTrainingTestScores') ? [] : ['className' => TraineeTrainingTestScoresTable::class];
        $this->TraineeTrainingTestScores = TableRegistry::getTableLocator()->get('TraineeTrainingTestScores', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeTrainingTestScores);

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
