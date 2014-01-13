<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This test will be skipped when running Codeception from the terminal.

// This test is used as a false positive when testing Webception. This test is used
// to test a failed test via the Webception RUN command.

if (isset($_SERVER['TERM_PROGRAM']) || isset($_SERVER['TERM']))
    $scenario->skip();

$I = new WebGuy($scenario);
$I->wantTo('fail at passing');

$I->assertTrue(FALSE);
