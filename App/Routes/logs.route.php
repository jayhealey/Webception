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
| Route: Log Check
|--------------------------------------------------------------------------
|
| This route returns an AJAX response to confirm if Codeception's Log folder is
| writeable by Webception.
|
*/

$app->get('/logs', function () use ($app) {

    $codeception  = $app->codeception;

    // There's the potential the codeception config isn't setup,
    //      so, set it to a default of NULL and we can still warn there's a problem.
    $log_path     = isset($codeception->config['paths']['log']) ?
                          $codeception->config['paths']['log'] : NULL;

    $response     = $codeception->checkWriteable(
        $log_path,
        $codeception->site->getConfig()
    );

    $http_status  = $response['ready'] ? 200 : 500;

    $app_response = $app->response();
    $app_response['Content-Type'] = 'application/json';
    $app_response->status($http_status);
    $app_response->body(json_encode($response));
});
