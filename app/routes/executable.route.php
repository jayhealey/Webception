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
| Route: Codeception Check
|--------------------------------------------------------------------------
|
| This route returns an AJAX response to confirm if Codeception is
| executable by Webception.
|
*/

$app->get('/executable', function () use ($app) {
    $codeception  = $app->codeception;

    $response     = $codeception->checkExecutable(
        $codeception->config['executable'],
        $codeception->config['location']
    );

    $http_status                  = $response['ready'] ? 200 : 500;
    $app_response                 = $app->response();
    $app_response['Content-Type'] = 'application/json';
    $app_response->status($http_status);
    $app_response->body(json_encode($response));
});
