<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LanguageAbilitiesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LanguageAbilitiesTable Test Case
 */
class LanguageAbilitiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LanguageAbilitiesTable
     */
    public $LanguageAbilities;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.LanguageAbilities',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('LanguageAbilities') ? [] : ['className' => LanguageAbilitiesTable::class];
        $this->LanguageAbilities = TableRegistry::getTableLocator()->get('LanguageAbilities', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LanguageAbilities);

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
