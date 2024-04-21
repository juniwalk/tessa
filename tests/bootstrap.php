<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Environment;
use Tester\Helpers;

if (@!include __DIR__.'/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

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
const TemporaryDir = DocumentRoot.'/temp';
const AssetsStorage = DocumentRoot.'/assets';
const OutputStorage = DocumentRoot.'/static';

Environment::setup();
Helpers::purge(TemporaryDir);


function createContainer(): Container
{
	$configurator = new Configurator;
	$configurator->setDebugMode(true);
	$configurator->setTempDirectory(TemporaryDir);
	$configurator->addConfig(__DIR__.'/config.neon');
	$configurator->addStaticParameters([
		'wwwDir' => DocumentRoot,
	]);

	return $configurator->createContainer();
}
