<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TraineeCoursesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TraineeCoursesTable Test Case
 */
class TraineeCoursesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TraineeCoursesTable
     */
    public $TraineeCourses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TraineeCourses',
        'app.Trainees',
        'app.VocationalTrainingInstitutions',
        'app.CourseMajors',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TraineeCourses') ? [] : ['className' => TraineeCoursesTable::class];
        $this->TraineeCourses = TableRegistry::getTableLocator()->get('TraineeCourses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TraineeCourses);

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
