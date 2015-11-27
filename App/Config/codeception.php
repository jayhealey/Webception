<?php

    /*
    |--------------------------------------------------------------------------
    | Default Configurations
    |--------------------------------------------------------------------------
    |
    | This file holds the default configurations for WebCeption. Rather than editing
    | this file copy `codeception-local-sample.php` to `codeception-local.php` and
    | update that file with your custom configs.
    |
    */

$localConfig = array();
if (file_exists(__DIR__.'/codeception-local.php')) {
    $localConfig = require(__DIR__.'/codeception-local.php');
}

return array_merge_recursive(array(

    /*
    |--------------------------------------------------------------------------
    | Codeception Configurations
    |--------------------------------------------------------------------------
    |
    | This is where you add your Codeception configurations.
    |
    | Webception allows you to have access test suites for multiple applications.
    |
    | Place them in the order you want and they'll appear in the drop-down list
    | in the front-end. The first site in the list will become the default
    | site that's loaded on session load.
    |
    | Just add the site name and full path to the 'codeception.yml' below and you're set.
    |
    */

    'sites' => array(
        'Webception'         => dirname(__FILE__) .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'
            .DIRECTORY_SEPARATOR.'codeception.yml',
    ),

    /*
    |--------------------------------------------------------------------------
    | Execute Codeception as a PHP command
    |--------------------------------------------------------------------------
    */
    'run_php'        => TRUE,

    /*
    |--------------------------------------------------------------------------
    | Codeception Executable
    |--------------------------------------------------------------------------
    |
    | Codeception is installed as a dependancy of Webception via Composer.
    |
    | You might need to set 'sudo chmod a+x vendor/bin/codecept' to allow Apache
    | to execute the Codeception executable.
    |
    */

    'executable' =>
        dirname(__FILE__) .
        DIRECTORY_SEPARATOR.'..'.
        DIRECTORY_SEPARATOR.'..'.
        DIRECTORY_SEPARATOR.'vendor'.
        DIRECTORY_SEPARATOR.'codeception'.
        DIRECTORY_SEPARATOR.'codeception'.
        DIRECTORY_SEPARATOR.'codecept',


    /*
    |--------------------------------------------------------------------------
    | You get to decide which type of tests get included.
    |--------------------------------------------------------------------------
    */

    'tests' => array(
        'acceptance' => TRUE,
        'functional' => TRUE,
        'unit'       => TRUE,
    ),

    /*
    |--------------------------------------------------------------------------
    | When we scan for the tests, we need to ignore the following files.
    |--------------------------------------------------------------------------
    */

    'ignore' => array(
        'WebGuy.php',
        'TestGuy.php',
        'CodeGuy.php',
        'AcceptanceTester.php',
        'FunctionalTester.php',
        'UnitTester.php',
        '_bootstrap.php',
        '.DS_Store',
    ),

    /*
    |--------------------------------------------------------------------------
    | Setting the location as the current file helps with offering information
    | about where this configuration file sits on the server.
    |--------------------------------------------------------------------------
    */

    'location'   => __FILE__,

    /*
    |--------------------------------------------------------------------------
    | Setting a Directory seperator in the configuration.
    | @todo Implement config driven seperator inplace of DIRECTORY_SEPERATOR
    |--------------------------------------------------------------------------
    */
    'DS'        => DIRECTORY_SEPARATOR,

    /*
    |--------------------------------------------------------------------------
    | Setting whether to pass additional run commands
    |--------------------------------------------------------------------------
    */
    'debug'        => FALSE,
    'steps'        => TRUE,
), $localConfig);
