<?php namespace App\Lib;

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Codeception
{
    /**
     * List of the Test sites
     *
     * @var array
     */
    private $sites;

    /**
     * Configuration for Codeception
     *
     * Merges the Codeception.yml and Webception Codeception.php
     *
     * @var boolean
     */
    public $config = FALSE;

    /**
     * Temporary copy of the Codeception.yml setup.
     *
     * If this is set, it means the configuration was loaded
     *
     * @var bool
     */
    private $yaml = FALSE;

    /**
     * Tally of all the tests that have been loaded
     *
     * @var integer
     */
    private $tally = 0;

    /**
     * List of all the tests
     *
     * @var array
     */
    private $tests = array();

    /**
     * Initialization of the Codeception class.
     *
     * @param array $config The codeception.php configuration file.
     */
    public function __construct($config = array(), $site = NULL)
    {
        // Set the basic config, just incase.
        $this->config = $config;

        // If the array wasn't loaded, we can't go any further.
        if (sizeof($config) == 0)
            return;

        // Setup the sites available to Webception
        $this->site = $site;

        // If the site class isn't ready, we can't load codeception.
        if (! $site->ready())
            return;

        // If the Configuration was loaded successfully, merge the configs!
        if ($this->yaml = $this->loadConfig($site->getConfigPath(), $site->getConfigFile())) {
            $this->config = array_merge($config, $this->yaml);
            $this->loadTests();
        }
    }

    /**
     * Return if Codeception is ready.
     *
     * @return boolean
     */
    public function ready()
    {
        return $this->yaml !== FALSE;
    }

    /**
     * Load the Codeception YAML configuration.
     *
     * @param  string $path
     * @param  string $file
     * @return array  $config
     */
    public function loadConfig($path, $file)
    {
        $full_path = $path . $file;

        // If the Codeception YAML can't be found, the application can't go any further.
        if (! file_exists($full_path))
            return false;

        // Using Symfony's Yaml parser, the file gets turned into an array.
        $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($full_path));

        // Update the config to include the full path.
        foreach ($config['paths'] as $key => &$test_path) {
            $test_path = file_exists($path . $test_path) ?
                 realpath($path . $test_path) : $path . $test_path;
        }

        return $config;
    }

    /**
     * Load the Codeception tests from disk.
     */
    public function loadTests()
    {
        if (! isset($this->config['tests']))
            return;

        foreach ($this->config['tests'] as $type => $active) {

            // If the test type has been disabled in the Webception config,
            //      skip processing the directory read for those tests.
            if (! $active)
                break;

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator("{$this->config['paths']['tests']}/{$type}/", \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            // Iterate through all the files, and filter out
            //      any files that are in the ignore list.
            foreach ($files as $file) {

                if (! in_array($file->getFilename(), $this->config['ignore'])
                   && $file->isFile())
                {
                    // Declare a new test and add it to the list.
                    $test = new Test();
                    $test->init($type, $file);
                    $this->addTest($test);
                    unset($test);
                }

            }
        }
    }

    /**
     * Add a Test to the list.
     *
     * Push the tally count up as well.
     *
     * @param Test $test
     */
    public function addTest(Test $test)
    {
        $this->tally++;
        $this->tests[$test->getType()][$test->getHash()] = $test;
    }

    /**
     * Get the complete test list.
     *
     * @param array $test List of loaded Tests.
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Given a test type & hash, return a single Test.
     *
     * @param  string       $type Test type (Unit, Acceptance, Functional)
     * @param  string       $hash Hash of the test.
     * @return App\Lib\Test or FALSE.
     */
    public function getTest($type, $hash)
    {
        if (isset($this->tests[$type][$hash]))
            return $this->tests[$type][$hash];

        return FALSE;
    }

    /**
     * Return the count of discovered tests
     *
     * @return integer $this->tally
     */
    public function getTestTally()
    {
        return $this->tally;
    }

    /**
     * Given a test, run the Codeception test.
     *
     * @param  Test $test Current test to Run.
     * @return Test $test Updated test with log and result.
     */
    public function run(Test $test)
    {
        // Get the full command path to run the test.
        $command = $this->getCommandPath($test->getType(), $test->getFilename());

        // Attempt to set the correct writes to Codeceptions Log path.
        @chmod($this->getLogPath(), 0777);

        // Run the helper function (as it's not specific to Codeception)
        // which returns the result of running the terminal command into an array.
        $output  = run_terminal_command($command);

        // Add the log to the test which also checks to see if there was a pass/fail.
        $test->setLog($output);

        return $test;
    }

    /**
     * Get the Codeception log path
     *
     * @return  string
     */
    public function getLogPath()
    {
        return $this->config['paths']['log'];
    }

    /**
     * Full command to run a Codeception test.
     *
     * @param  string $type     Test Type (Acceptance, Functional, Unit)
     * @param  string $filename Name of the Test
     * @return string Full command to execute Codeception with requred parameters.
     */
    public function getCommandPath($type, $filename)
    {
        // Build all the different parameters as part of the console command
        $params = array(
            $this->config['executable'],        // Codeception Executable
            "run",                              // Command to Codeception
            "--no-colors",                      // Forcing Codeception to not use colors, if enabled in codeception.yml
            "--config=\"{$this->site->getConfig()}\"", // Full path & file of Codeception
            $type,                              // Test Type (Acceptance, Unit, Functional)
            $filename,                          // Filename of the Codeception test
            "2>&1"                              // Added to force output of running executable to be streamed out
        );

        // Build the command to be run.
        return implode(' ', $params);
    }

    /**
     * Given a test type & hash, handle the test run response for the AJAX call.
     *
     * @param  string $type Test type (Unit, Acceptance, Functional)
     * @param  string $hash Hash of the test.
     * @return array  Array of flags used in the JSON respone.
     */
    public function getRunResponse($type, $hash)
    {
        $response = array(
            'message'     => NULL,
            'run'         => FALSE,
            'passed'      => FALSE,
            'state'       => 'error',
            'log'         => NULL
        );

        // If Codeceptions not properly configured, the test won't be found
        // and it won't be run.
        if (! $this->ready())
            $response['message'] = 'The Codeception configuration could not be loaded.';

        // If the test can't be found, we can't run the test.
        if (! $test = $this->getTest($type, $hash))
            $response['message'] = 'The test could not be found.';

        // If there's no error message set yet, it means we're good to go!
        if (is_null($response['message'])) {

            // Run the test!
            $test               = $this->run($test);
            $response['run']    = $test->ran();
            $response['log']    = $test->getLog();
            $response['passed'] = $test->passed();
            $response['state']  = $test->getState();
            $response['title']  = $test->getTitle();
        }

        return $response;
    }

    /**
     * Check if the Codeception Log Path is writeable.
     *
     * @param string Path that Codeception writes to log.
     * @param string Config location for Codeception.
     * @return array Array of flags used in the JSON respone.
     */
    public function checkWriteable($path=null, $config)
    {
        $response             = array();
        $response['resource'] = $path;

        // Set this to ensure the developer knows there $path was set.
        $response['config']   = ($config);

        if (is_null($path)) {
            $response['error'] = 'The Codeception Log is not set. Is the Codeception configuration set up?';
        } elseif (! file_exists($path)) {
            $response['error'] = 'The Codeception Log directory does not exist. Please check the following path exists:';
        } elseif (! is_writeable($path)) {
            $response['error'] = 'The Codeception Log directory can not be written to yet. Please check the following path has \'chmod 777\' set:';
        }

        $response['ready'] = ! isset($response['error']);

        return $response;
    }

    /**
     * Check that the Codeception executable exists and is runnable.
     *
     * @param  string $file   File name of the Codeception executable.
     * @param  string $config Full path of the config of where the $file was defined.
     * @return array  Array of flags used in the JSON respone.
     */
    public function checkExecutable($file, $config)
    {
        $response             = array();
        $response['resource'] = $file;

        // Set this to ensure the developer knows there $file was set.
        $response['config']   = realpath($config);

        if (! file_exists($file)) {
            $response['error'] = 'The Codeception executable could not be found.';
        } elseif ( ! is_executable($file) && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $response['error'] = 'Codeception isn\'t executable. Have you set executable rights to the following (try chmod o+x).';
        }

        // If there wasn't an error, then it's good!
        $response['ready'] = ! isset($response['error']);

        return $response;
    }
}
