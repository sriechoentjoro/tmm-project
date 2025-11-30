<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SpecialSkillSupportInstitutionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SpecialSkillSupportInstitutionsTable Test Case
 */
class SpecialSkillSupportInstitutionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SpecialSkillSupportInstitutionsTable
     */
    public $SpecialSkillSupportInstitutions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.SpecialSkillSupportInstitutions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SpecialSkillSupportInstitutions') ? [] : ['className' => SpecialSkillSupportInstitutionsTable::class];
        $this->SpecialSkillSupportInstitutions = TableRegistry::getTableLocator()->get('SpecialSkillSupportInstitutions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SpecialSkillSupportInstitutions);

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
