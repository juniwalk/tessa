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

final class TessaExtension extends CompilerExtension
{
	/** @var string[] */
	private $default = [
		'outputDir' => null,
		'checkLastModified' => true,
		'filters' => [],
	];

	/** @var string[] */
	private $bundle = [
		'joinFiles' => false,
		'defer' => false,
		'extend' => null,
		'assets' => [],
	];


	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$bundles = array_diff_key($config, $this->default);
		$config = array_intersect_key($config, $this->default);
		$config = $this->validateConfig($this->default, $config);

		$storage = $builder->addDefinition($this->prefix('storage'))
			->setFactory(Storage::class, [$config['outputDir']])
			->addSetup('setCheckLastModified', [$config['checkLastModified']]);

		foreach ($config['filters'] as $filter) {
			$storage->addSetup('addFilter', [$filter]);
		}

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(BundleManager::class, [$builder->parameters['wwwDir']]);

		foreach ($bundles as $name => $params) {
			$params = $this->validateConfig($this->bundle, $params);
			$assets = $this->fileToAsset($params['assets']);

			$bundle = new AssetBundle($name, ... $assets);
			$bundle->setJoinFiles($params['joinFiles']);
			$bundle->setDeferred($params['defer']);
			$bundle->setExtendBundle($params['extend']);

			$manager->addSetup('addBundle', [$bundle]);
		}
	}


	/**
	 * @param  string[]  $files
	 * @return Asset[]
	 */
	private function fileToAsset(iterable $files): iterable
	{
		$assets = [];

		foreach ($files as $file) {
			if (Assets\HttpAsset::match($file)) {
				$assets[] = new Assets\HttpAsset($file);
				continue;
			}

			if (Assets\ScssAsset::match($file)) {
				$assets[] = new Assets\ScssAsset($file);
				continue;
			}

			$assets[] = new Assets\FileAsset($file);
		}

		return $assets;
	}
}
