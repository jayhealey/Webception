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
$I->wantTo('run test that does not exist to confirm the response');
$I->amOnPage('run/lol/fake-test-id');
$I->see('{"message":"The test could not be found.","run":false,"passed":false,"state":"error","log":null}');
