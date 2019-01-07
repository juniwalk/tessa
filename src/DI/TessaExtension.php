<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\DI;

use JuniWalk\Tessa\Assets\HttpAsset;
use JuniWalk\Tessa\Assets\FileAsset;
use JuniWalk\Tessa\BundleManager;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Storage;
use JuniWalk\Tessa\TessaControl;

final class TessaExtension extends \Nette\DI\CompilerExtension
{
    /** @var string[] */
	private $default = [
		'outputDir' => NULL,
		'checkLastModified' => TRUE,
		'filters' => [],
	];

    /** @var string[] */
	private $bundle = [
		'joinFiles' => FALSE,
		'extend' => NULL,
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
			->setClass(BundleManager::class);

        foreach ($bundles as $name => $params) {
            $params = $this->validateConfig($this->bundle, $params);
			$assets = $this->fileToAsset($params['assets']);

			$bundle = new AssetBundle($name, ... $assets);
			$bundle->setJoinFiles($params['joinFiles']);
			$bundle->setExtendBundle($params['extend']);

			$manager->addSetup('addBundle', [$bundle]);
        }

		$manager = $builder->addDefinition($this->prefix('tessa'))
			->setFactory(TessaControl::class, [$builder->parameters['wwwDir']]);
	}


    /**
     * @return void
     */
	public function beforeCompile()
	{
	}


	/**
	 * @param  string[]  $assets
	 * @return Asset[]
	 */
	private function fileToAsset(iterable $files): iterable
	{
		$assets = [];

		foreach ($files as $file) {
			if (preg_match('/https?/i', $file)) {
				$assets[] = new HttpAsset($file);
				continue;
			}

			$assets[] = new FileAsset($file);
		}

		return $assets;
	}
}
