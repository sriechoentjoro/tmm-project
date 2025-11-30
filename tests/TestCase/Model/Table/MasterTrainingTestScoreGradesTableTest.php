<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterTrainingTestScoreGradesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterTrainingTestScoreGradesTable Test Case
 */
class MasterTrainingTestScoreGradesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterTrainingTestScoreGradesTable
     */
    public $MasterTrainingTestScoreGrades;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterTrainingTestScoreGrades',
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
        $config = TableRegistry::getTableLocator()->exists('MasterTrainingTestScoreGrades') ? [] : ['className' => MasterTrainingTestScoreGradesTable::class];
        $this->MasterTrainingTestScoreGrades = TableRegistry::getTableLocator()->get('MasterTrainingTestScoreGrades', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterTrainingTestScoreGrades);

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
