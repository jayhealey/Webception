<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class WebceptionClassTestsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * Test Object
     *
     * @var boolean
     */
    private $test;

    /**
     * Test File
     *
     * @var  boolean
     */
    private $test_file;

    private $type            = 'unit';
    private $filter          = array();

    protected function _before()
    {
        $this->test = new \App\Lib\Test();

        // Load up the current test file as an example test.
        $this->test_file = new SplFileInfo(__FILE__);
    }

    protected function _after()
    {
    }

    public function testDefaultSettings()
    {
        $test = $this->test;
        $this->assertFalse($test->passed());
        $this->assertFalse($test->ran());
        $this->assertEquals(sizeof($test->getLog(FALSE)), 0);
    }

    public function testSettingOfTestData()
    {
        $test          = $this->test;
        $test_file     = $this->test_file;

        // Mock how the test object handles the test data.
        $filename      = $test->filterFileName($test_file->getFileName());
        $hash          = $test->makeHash($this->type . $filename);
        $title         = $test->filterTitle($filename);

        // Initialize the test as per usual
        $test->init($this->type, $test_file);

        // Confirm the mocked test data is filtered and set properly in the test object
        $this->assertEquals($hash, $test->getHash());
        $this->assertEquals($title, $test->getTitle());
        $this->assertEquals($this->type, $test->getType());
        $this->assertEquals($test_file->getFileName(), $test->getFilename());

        // Confirm a new test has not passed (as it's done nothing)
        $this->assertEquals(false, $test->passed());

        // Falsify a passed test
        $test->setPassed();
        $this->assertEquals(true, $test->passed());
    }

    public function testLog()
    {
        $test      = $this->test;
        $test_file = $this->test_file;

        $test->init($this->type, $test_file);

        // Confirm the log is empty
        $this->assertFalse($test->ran());
        $this->assertEquals(sizeof($test->getLog(FALSE)), 0);

        // Add a line to the log
        $log_line = 'Example Log Line';
        $test->setLog(array('Example Log Line'));

        // Confirm the item was added
        $log = $test->getLog(FALSE);
        $this->assertEquals($log[0], $log_line);
        $this->assertEquals(sizeof($test->getLog()), 1);
        $this->assertTrue($test->ran());

        // Reset the test
        $test->reset();

        // Confirm the log is empty
        $this->assertFalse($test->ran());
        $this->assertEquals(sizeof($test->getLog(FALSE)), 0);
    }

}
