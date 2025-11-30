<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeEducationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeEducationsTable Test Case
 */
class TraineeEducationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeEducationsTable
     */
    public $TraineeEducations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeEducations',
        'app.Trainees',
        'app.MasterStratas',
        'app.MasterPropinsis',
        'app.MasterKabupatens',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeEducations') ? [] : ['className' => TraineeEducationsTable::class];
        $this->TraineeEducations = TableRegistry::getTableLocator()->get('TraineeEducations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeEducations);

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
