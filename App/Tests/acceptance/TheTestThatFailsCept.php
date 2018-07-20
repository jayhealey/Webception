<?php
// We don't want to run this on the terminal because we only need this
// test so we can check that the web-interface handles failures correctly. The
// terminal is for checking that Webception actually works - so having fake
// failures isn't helpful.
// 
// When running the tests on the command line, you should add
// --skip-group=web-interface-only
// 
// after 'run'. E.g: `codecept run --skip-group=web-interface-only acceptance`
// 
// @group web-interface-only
// 
/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This test is used as a false positive when testing Webception. This test is used
// to test a failed test via the Webception RUN command.

$I = new WebGuy($scenario);
$I->wantTo('fail at passing');

$I->assertTrue(FALSE);
