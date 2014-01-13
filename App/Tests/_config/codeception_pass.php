<?php

/*
|--------------------------------------------------------------------------
| Test Settings for Codeception
|--------------------------------------------------------------------------
|
| All of the settings in this Configuration file are set to work.
|
| When Webception is run in Test Mode, we load this config to ensure
| everything works as expected.
|
*/

return array(

    /*
    |--------------------------------------------------------------------------
    | Path of the Codeception.yml
    |--------------------------------------------------------------------------
    */

    'sites' => array(

        'Webception'          => dirname(__FILE__) .'/codeception_pass.yml',
        'Another Webception'  => dirname(__FILE__) .'/codeception_pass.yml',

    ),

    /*
    |--------------------------------------------------------------------------
    | Dummy Codeception executable (that I use in testing)
    |--------------------------------------------------------------------------
    */

    'executable' => dirname(__FILE__) .'/../../../vendor/bin/codecept',

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
        '_bootstrap.php',
    ),

    'location'   => __FILE__,
);
