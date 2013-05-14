<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

$loader   = require_once __DIR__ . '/../app/bootstrap.php.cache';
$response = new \Symfony\Component\HttpFoundation\RedirectResponse('app.php');

$response->send();
