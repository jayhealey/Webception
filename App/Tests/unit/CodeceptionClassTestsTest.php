<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CodeceptionClassTestsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    private $codeception = FALSE;
    private $config      = FALSE;

    private $test_filename = 'WebceptionTestClassTest.php';
    private $test_file;
    private $pass_filename = 'TheTestThatPassesCept.php';
    private $fail_filename = 'TheTestThatFailsCept';
    private $test;
    private $type          = 'unit';
    private $site_name = 'Webception';

    protected function _before()
    {
        // Load the Codeception config
        $this->config = require(dirname(__FILE__) . '/../_config/codeception_pass.php');

        // Load up a working Site class
        $this->site = new \App\Lib\Site($this->config['sites']);

        $this->site->set(md5($this->site_name));

        // Load up Codeception class
        $this->codeception = new \App\Lib\Codeception($this->config, $this->site);

        // Load up a blank test
        $this->test = new \App\Lib\Test();

        // Load up the current test file as an example test.
        $this->test_file = new SplFileInfo($this->test_filename);
    }

    protected function _after()
    {
    }

    /**
     * Test the Codeception class without passing in a configuration
     *
     */
    public function testCodeceptionWithNoConfig()
    {
        $codeception = new \App\Lib\Codeception();
        $this->assertFalse($codeception->ready());
    }

    /**
     * Test the Codeception class with configuration where
     * is completely empty.
     */
    public function testCodeceptionWithEmptyConfig()
    {
        $config      = require(dirname(__FILE__) . '/../_config/codeception_fail.php');

        $site        = new \App\Lib\Site($config['sites']);
        $site->set(md5($this->site_name));
        $codeception = new \App\Lib\Codeception($config, $site);
        $this->assertFalse($codeception->ready());
    }

    public function testCodeceptionWithInvalidExecutable()
    {
        $config      = require(dirname(__FILE__) . '/../_config/codeception_executable_fail.php');
        $site        = new \App\Lib\Site($this->config['sites']);
        $site->set(md5($this->site_name));
        $codeception = new \App\Lib\Codeception($config, $site);
        $response    = $codeception->checkExecutable($config['executable'], $config['location']);
        $this->assertFalse($response['ready']);
    }

    public function testCodeceptionWithValidConfig()
    {
        $codeception = $this->codeception;
        $config      = $this->config;
        $this->assertTrue($codeception->ready());

        $response = $codeception->checkExecutable($config['executable'], $config['location']);
        $this->assertTrue($response['ready']);
    }

    public function testCodeceptionCommandPath()
    {
        $codeception = $this->codeception;
        $config      = $this->config;

        $params = array(
            $this->config['executable'],        // Codeception Executable
            "run",                              // Command to Codeception
            "--no-colors",                      // Forcing Codeception to not use colors, if enabled in codeception.yml
            "--config=\"{$codeception->site->getConfig()}\"", // Full path & file of Codeception
            $this->type,                              // Test Type (Acceptance, Unit, Functional)
            $this->test_filename,                          // Filename of the Codeception test
            "2>&1"                              // Added to force output of running executable to be streamed out
        );

        $mock_command        = implode(' ', $params);
        $codeception_command = $codeception->getCommandPath($this->type, $this->test_filename);

        $this->assertEquals($mock_command, $codeception_command);
    }

    /**
     * Test the adding and getting of tests when they're loaded.
     */
    public function testAddingAndGettingTests()
    {
        $test         = $this->test;
        $codeception  = $this->codeception;
        $tally_before = $codeception->getTestTally();
        $type         = 'awesome'; // Fake type. So we don't clash with the loaded tests.

        // Initialize the test as per usual
        $test->init($type, $this->test_file);

        // Check the adding tally works
        $codeception->addTest($test);
        $tally_after = $codeception->getTestTally();
        $this->assertGreaterThan($tally_before, $tally_after);

        // Check the test can be recieved again
        $test_back = $codeception->getTest($type, $test->getHash());
        $this->assertEquals($test_back->getTitle(), $test->getTitle());
    }

    /**
     * Testing the getRunResponse used in the run route for a test that wasn't found.
     */
    public function testResponseForNotFoundTest()
    {
        $codeception  = $this->codeception;
        $response     = $codeception->getRunResponse('lol', md5("notgoingtofindthis"));

        $this->assertFalse($response['run']);
        $this->assertFalse($response['passed']);
        $this->assertEquals($response['message'], 'The test could not be found.');
        $this->assertNull($response['log']);
        $this->assertEquals($response['state'], 'error');
    }

    /**
     * Testing the run() function on the Test class with a passing test.
     */
    public function testTheTestThatPasses()
    {
        $codeception  = $this->codeception;

        // Get the test that we know fails.
        $test = $codeception->getTest('acceptance', md5("acceptanceTheTestThatPasses"));

        // Confirm the test hasn't been or passed yet
        $this->assertFalse($test->ran());
        $this->assertFalse($test->passed());

        // Run the test, which generates the log and analyes the log as it gets added.
        $test = $codeception->run($test);

        // Confirm the test was run & passed!
        $this->assertTrue($test->ran());
        $this->assertTrue($test->passed());
    }

    /**
     * Testing the getRunResponse used in the run route for a passing test.
     */
    public function testResponseForTheTestThatPasses()
    {
        $codeception  = $this->codeception;
        $response     = $codeception->getRunResponse('acceptance', md5("acceptanceTheTestThatPasses"));

        $this->assertTrue($response['run']);
        $this->assertTrue($response['passed']);
        $this->assertNull($response['message']);
        $this->assertNotNull($response['log']);
        $this->assertEquals($response['state'], 'passed');
    }

    /**
     * Testing the run() function on the Test class with a failing test.
     */
    public function testTheTestThatFails()
    {
        $codeception  = $this->codeception;

        // Get the test that we know fails.
        $test = $codeception->getTest('acceptance', md5("acceptanceTheTestThatFails"));

        // Confirm the test hasn't been or passed yet
        $this->assertFalse($test->ran());
        $this->assertFalse($test->passed());

        // Run the test, which generates the log and analyes the log as it gets added.
        $test = $codeception->run($test);

        // Confirm the test was run but it's not passed
        $this->assertTrue($test->ran());
        $this->assertFalse($test->passed());
    }

    /**
     * Testing the getRunResponse used in the run route for a failing test.
     */
    public function testResponseForTheTestThatFails()
    {
        $codeception  = $this->codeception;
        $response     = $codeception->getRunResponse('acceptance', md5("acceptanceTheTestThatFails"));

        $this->assertTrue($response['run']);
        $this->assertFalse($response['passed']);
        $this->assertNull($response['message']);
        $this->assertNotNull($response['log']);
        $this->assertEquals($response['state'], 'failed');
    }

}
