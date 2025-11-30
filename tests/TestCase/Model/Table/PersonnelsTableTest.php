<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PersonnelsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PersonnelsTable Test Case
 */
class PersonnelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PersonnelsTable
     */
    public $Personnels;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Personnels',
        'app.Genders',
        'app.Religions',
        'app.MaritalStatuses',
        'app.EmployeeStatuses',
        'app.Propinsis',
        'app.Kabupatens',
        'app.Kecamatans',
        'app.Kelurahans',
        'app.Companies',
        'app.Departments',
        'app.Positions',
        'app.Stratas',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Personnels') ? [] : ['className' => PersonnelsTable::class];
        $this->Personnels = TableRegistry::getTableLocator()->get('Personnels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Personnels);

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
