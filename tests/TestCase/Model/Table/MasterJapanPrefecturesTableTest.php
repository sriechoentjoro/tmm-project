<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterJapanPrefecturesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterJapanPrefecturesTable Test Case
 */
class MasterJapanPrefecturesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterJapanPrefecturesTable
     */
    public $MasterJapanPrefectures;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MasterJapanPrefectures',
        'app.AcceptanceOrganizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MasterJapanPrefectures') ? [] : ['className' => MasterJapanPrefecturesTable::class];
        $this->MasterJapanPrefectures = TableRegistry::getTableLocator()->get('MasterJapanPrefectures', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MasterJapanPrefectures);

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
