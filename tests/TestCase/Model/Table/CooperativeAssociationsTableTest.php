<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CooperativeAssociationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CooperativeAssociationsTable Test Case
 */
class CooperativeAssociationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CooperativeAssociationsTable
     */
    public $CooperativeAssociations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CooperativeAssociations',
        'app.CooperativeAssociationStories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CooperativeAssociations') ? [] : ['className' => CooperativeAssociationsTable::class];
        $this->CooperativeAssociations = TableRegistry::getTableLocator()->get('CooperativeAssociations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CooperativeAssociations);

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
