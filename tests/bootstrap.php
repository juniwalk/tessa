<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Environment;
use Tester\Helpers;

define('ProcessId', getmypid());

if (@!include __DIR__.'/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

const DocumentRoot = __DIR__.'/.documentRoot';
const TemporaryDir = DocumentRoot.'/temp/'.ProcessId;
const AssetsStorage = DocumentRoot.'/assets';
const OutputStorage = DocumentRoot.'/static';

Environment::setup();
Helpers::purge(DocumentRoot.'/log');
Helpers::purge(TemporaryDir);


function createContainer(): Container
{
	$configurator = new Configurator;
	$configurator->setDebugMode(true);
	$configurator->enableTracy(DocumentRoot.'/log');
	$configurator->setTempDirectory(TemporaryDir);
	$configurator->addConfig(__DIR__.'/config.neon');
	$configurator->addStaticParameters([
		'wwwDir' => DocumentRoot,
		'pid' => ProcessId,
	]);

	return $configurator->createContainer();
}
