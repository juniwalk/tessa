<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\BundleNotFoundException;
use JuniWalk\Tessa\Exceptions\BundleRecursionException;

final class BundleManager
{
	/** @var array<string, Bundle> */
	private array $bundles = [];

	public function __construct(
		private ?Storage $storage = null,
	) {
	}


	public function setStorage(?Storage $storage): void
	{
		$this->storage = $storage;
	}


	public function addBundle(Bundle $bundle): void
	{
		$this->bundles[$bundle->getName()] = $bundle;
	}


	/**
	 * @throws BundleNotFoundException
	 */
	public function getBundle(string $bundle): Bundle
	{
		if (!isset($this->bundles[$bundle])) {
			throw new BundleNotFoundException($bundle);
		}

		return $this->bundles[$bundle];
	}


	/**
	 * @return string[]
	 */
	public function getBundles(): array
	{
		return array_keys($this->bundles);
	}


	/**
	 * @throws BundleNotFoundException
	 * @throws BundleRecursionException
	 */
	public function compile(string $bundleName, Type $type): Bundle
	{
		$bundle = $this->getBundle($bundleName);
		$bundleType = $bundle->getAttribute('type');
		$bundleName = $bundleName.$type->value;
		$assets = [];

		$this->detectRecursion($bundle);

		if ($extend = $bundle->getExtendBundle()) {
			$assets = $this->compile($extend, $type)->getAssets();
		}

		foreach ($bundle->getAssets($type) as $asset) {
			if (!$asset->isModule() && $bundleType <> 'module') {
				$asset = $this->storage?->store($asset, $bundleName) ?? $asset;
			}

			$assets[] = $asset;
		}

		$output = new AssetBundle($bundleName, ...$assets);
		$output->setAttributes($bundle->getAttributes());

		return $output;
	}


	/**
	 * @param  array<string, bool> $history
	 * @return array<string, bool>
	 * @throws BundleRecursionException
	 */
	private function detectRecursion(Bundle $bundle, array $history = []): array
	{
		$history[$bundle->getName()] = true;

		if ($extend = $bundle->getExtendBundle()) {
			if ($history[$extend] ?? false) {
				throw BundleRecursionException::fromBundle($bundle, $extend);
			}

			$history = $this->detectRecursion(
				$this->getBundle($extend),
				$history,
			);
		}

		return $history;
	}
}
