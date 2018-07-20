<?php
/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This test is used as a positive when testing Webception. This test is used
// to test a failed test via the Webception RUN command.

$I = new WebGuy($scenario);
$I->wantTo('do nothing and pass!');
