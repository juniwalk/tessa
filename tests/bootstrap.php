<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use Tester\Environment;

require __DIR__.'/../vendor/autoload.php';

// Setup environment
$_SERVER = [
	'HTTPS' => 'On',
	'HTTP_HOST' => 'nette.org:8080',
	'QUERY_STRING' => 'view=default',
	'REMOTE_ADDR' => '192.168.188.66',
	'REQUEST_METHOD' => 'GET',
	'REQUEST_URI' => '/index.php?view=default',
	'SCRIPT_NAME' => '/index.php',
];

const DocumentRoot = __DIR__.'/.documentRoot';
const AssetsStorage = DocumentRoot.'/assets';
const OutputStorage = DocumentRoot.'/static';

Environment::setup();
