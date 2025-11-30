<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeTrainingBatchesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeTrainingBatchesTable Test Case
 */
class TraineeTrainingBatchesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeTrainingBatchesTable
     */
    public $TraineeTrainingBatches;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeTrainingBatches',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeTrainingBatches') ? [] : ['className' => TraineeTrainingBatchesTable::class];
        $this->TraineeTrainingBatches = TableRegistry::getTableLocator()->get('TraineeTrainingBatches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeTrainingBatches);

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
