<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$I = new WebGuy($scenario);
$I->wantTo('check AJAX call when the Codeception log check fails.');
$I->sendGET('logs?test=log_fail');
$I->seeResponseContainsJson(array(
    'ready' => false,
    'error' => "The Codeception Log directory does not exist. Please check the following path exists:",
));
