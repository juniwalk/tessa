<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\DI;

use JuniWalk\Tessa\BundleManager;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Commands\TessaWarmUpCommand;
use JuniWalk\Tessa\Storage;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\InvalidConfigurationException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class TessaExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::from(new Config, [
			'filters' => Expect::listOf(
				Expect::string()->dynamic()->transform(fn($stmt) => match (true) {
					$stmt instanceof Statement => $stmt,
					default => new Statement($stmt),
				})
			),
		])

		->otherItems(Expect::from(new Bundle))
		->skipDefaults();
	}


	/**
	 * @throws InvalidConfigurationException
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		if (!$config instanceof Config) {
			throw new InvalidConfigurationException('Config must be instance of '.Config::class);
		}

		$storage = $builder->addDefinition($this->prefix('storage'))
			->setFactory(Storage::class, [$config->outputDir])
			->addSetup('setCheckLastModified', [$config->checkLastModified])
			->addSetup('setDebugMode', [$config->debugMode]);

		foreach ($config->filters as $filter) {
			$storage->addSetup('addFilter', [$filter]);
		}

		$manager = $builder->addDefinition($this->prefix('manager'))
			->setFactory(BundleManager::class, [$builder->parameters['wwwDir']])
			->addSetup('setDirectLinking', [$config->directLinking]);

		foreach ($config->bundles as $name => $params) {
			$bundle = $builder->addDefinition($this->prefix('bundle.'.$name))
				->setFactory(AssetBundle::class, [$name])
				->addSetup('setExtendBundle', [$params->extend])
				->addSetup('setCookieConsent', [$params->cookieConsent])
				->addSetup('setJoinFiles', [$params->joinFiles])
				->addSetup('setModule', [$params->isModule])
				->addSetup('setDeferred', [$params->defer])
				->addSetup('setAsync', [$params->async]);

			foreach ($params->assets as $file) {
				$bundle->addSetup('discoverAsset', [$file]);
			}

			$manager->addSetup('addBundle', [$bundle]);
		}

		$builder->addDefinition($this->prefix('warmUp'))
			->setFactory(TessaWarmUpCommand::class);
	}
}
