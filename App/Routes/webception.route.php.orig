<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
|--------------------------------------------------------------------------
| Route: Dashboard
|--------------------------------------------------------------------------
|
| The dashboard is the homepage of Webception. It loads all the
| configuration and shows what tests are available to run.
|
*/

$app->get('/', function ($site = null) use ($app) {

    $tests       = FALSE;
    $test_count  = 0;
    $webception  = $app->config('webception');
    $codeception = $app->codeception;

    if ($codeception->ready()) {
        $tests      = $codeception->getTests();
        $test_count = $codeception->getTestTally();
    }

    $app->render('dashboard.html', array(
        'name'        => $app->getName(),
        'webception'  => $webception,
        'codeception' => $codeception,
        'tests'       => $tests,
        'test_count'  => $test_count,
    ));
});
