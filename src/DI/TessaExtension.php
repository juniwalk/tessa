<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\DI;

use JuniWalk\Tessa\Assets;
use JuniWalk\Tessa\BundleManager;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Storage;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class TessaExtension extends CompilerExtension
{
	/**
	 * @return Schema
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'outputDir' => Expect::string()->required(),
			'checkLastModified' => Expect::bool(true),
			'debugMode' => Expect::bool(false),
			'filters' => Expect::list(),
		])

		->otherItems(Expect::structure([
			'joinFiles' => Expect::bool(false),
			'defer' => Expect::bool(false),
			'extend' => Expect::string(),
			'assets' => Expect::list(),
		]));
	}


	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$storage = $builder->addDefinition($this->prefix('storage'))
			->setFactory(Storage::class, [$config->outputDir])
			->addSetup('setCheckLastModified', [$config->checkLastModified])
			->addSetup('setDebugMode', [$config->debugMode]);

		foreach ($config->filters as $filter) {
			$storage->addSetup('addFilter', [$filter]);
		}

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(BundleManager::class, [$builder->parameters['wwwDir']]);

		$bundles = array_diff_key((array) $config, [
			'outputDir' => null,
			'checkLastModified' => null,
			'debugMode' => null,
			'filters' => null,
		]);

		foreach ($bundles as $name => $params) {
			$bundle = $builder->addDefinition($this->prefix('bundle.'.$name))
				->setFactory(AssetBundle::class, [$name])
				->addSetup('setExtendBundle', [$params->extend])
				->addSetup('setJoinFiles', [$params->joinFiles])
				->addSetup('setDeferred', [$params->defer]);

			foreach ($params->assets as $file) {
				$bundle->addSetup('discoverAsset', [$file]);
			}

			$manager->addSetup('addBundle', [$bundle]);
		}
	}
}
