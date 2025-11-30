<?php
namespace App\Test\TestCase\Shell\Helper;

use App\Shell\Helper\ModelInfoHelper;
use Cake\Console\ConsoleIo;
use Cake\TestSuite\Stub\ConsoleOutput;
use Cake\TestSuite\TestCase;

/**
 * App\Shell\Helper\ModelInfoHelper Test Case
 */
class ModelInfoHelperTest extends TestCase
{
    /**
     * ConsoleOutput stub
     *
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    public $stub;

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo
     */
    public $io;

    /**
     * Test subject
     *
     * @var \App\Shell\Helper\ModelInfoHelper
     */
    public $ModelInfo;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new ConsoleOutput();
        $this->io = new ConsoleIo($this->stub);
        $this->ModelInfo = new ModelInfoHelper($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ModelInfo);

        parent::tearDown();
    }

    /**
     * Test output method
     *
     * @return void
     */
    public function testOutput()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
