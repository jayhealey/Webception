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
| Route: Test Runner
|--------------------------------------------------------------------------
|
| Given a test type (acceptance, functional etc) and a hash,
| load all the tests, find the test and then run it.
|
| The route is called via AJAX and the return repsonse is JSON.
|
*/

$app->get('/run/:type/:hash', function ($type, $hash) use ($app) {

    $response                     = $app->codeception->getRunResponse($type, $hash);
    $http_status                  = $response['run'] ? 200 : 500;

    $app_response                 = $app->response();
    $app_response['Content-Type'] = 'application/json';
    $app_response->status($http_status);
    $app_response->body(json_encode($response));

})->name('run');
