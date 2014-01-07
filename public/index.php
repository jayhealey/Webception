<?php

/**
 * Webception: Web Interface for running Codeception tests
 *
 * @package  Webception
 * @license  MIT
 * @version  0.1.0
 * @link     https://github.com/jayhealey/webception
 * @author   James Healey <jayhealey@gmail.com>
 */

/*
|--------------------------------------------------------------------------
| Application Bootstrap
|--------------------------------------------------------------------------
*/

session_cache_limiter(false);
session_start();

define('HASH', 'site_session');
define('RP', '../');
require(RP . 'vendor/autoload.php');
require(RP . 'App/bootstrap.php');

/*
|--------------------------------------------------------------------------
| Slim Bootstrap
|--------------------------------------------------------------------------
*/

$app = new \Slim\Slim(array(

    /*
    |--------------------------------------------------------------------------
    | Webception Settings
    |--------------------------------------------------------------------------
    */

    'webception' => array(
        'version'    => '0.1.0',
        'name'       => '<strong>Web</strong>ception',
        'repo'       => 'https://github.com/jayhealey/webception',
        'twitter'    => '@WebceptionApp',
        'config'     => RP . 'App/Config/codeception.php',
        'test'       => RP . 'App/Tests/_config/codeception_%s.php',
    ),

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    */

    'templates.path' => RP . 'App/Templates',

));

/*
|--------------------------------------------------------------------------
| Load the configuration
|--------------------------------------------------------------------------
*/

$config = get_webception_config($app);

/*
|--------------------------------------------------------------------------
| Sites Class
|--------------------------------------------------------------------------
*/

$app->container->singleton('site', function () use ($app, $config) {

    $hash_in_querystring = $app->request()->get('hash');

    // If the site is being changed (via query string), use that.
    // If a site is in session, use that.
    // Otherwise, set as false and the Site() class will pull the first site it can find.

    $hash = FALSE;

    if (! is_null($hash_in_querystring) && $hash_in_querystring !== FALSE)
        $hash = $hash_in_querystring;
    elseif (isset($_SESSION[HASH]))
        $hash = $_SESSION[HASH];

    // Setup the site class with all the available sites from the
    // Codeception configuration
    $site = new \App\Lib\Site($config['sites']);

    // Set the current site
    $site->set($hash);

    // Update the users session to use the chosen site
    $_SESSION[HASH] = $site->getHash();

    return $site;
});

/*
|--------------------------------------------------------------------------
| Codeception Class
|--------------------------------------------------------------------------
*/

$app->container->singleton('codeception', function () use ($config, $app) {
    return new \App\Lib\Codeception($config, $app->site);
});

/*
|--------------------------------------------------------------------------
| Load Routes
|--------------------------------------------------------------------------
*/

foreach (glob(RP . 'App/Routes/*.route.php') as $route)
    require_once($route);

/*
|--------------------------------------------------------------------------
| Setup Twig for Templates
|--------------------------------------------------------------------------
*/

$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset'          => 'utf-8',
    'cache'            => realpath(RP .'App/Templates/_cache'),
    'auto_reload'      => true,
    'strict_variables' => false,
    'autoescape'       => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

/*
|--------------------------------------------------------------------------
| Error Handler
|--------------------------------------------------------------------------
*/

$app->error(function (\Exception $e) use ($app) {
    $app->render('error.php');
});

$app->run();
