<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeScoreAveragesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeScoreAveragesTable Test Case
 */
class TraineeScoreAveragesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeScoreAveragesTable
     */
    public $TraineeScoreAverages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeScoreAverages',
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
        $config = TableRegistry::getTableLocator()->exists('TraineeScoreAverages') ? [] : ['className' => TraineeScoreAveragesTable::class];
        $this->TraineeScoreAverages = TableRegistry::getTableLocator()->get('TraineeScoreAverages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeScoreAverages);

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
