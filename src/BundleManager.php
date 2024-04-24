<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\BundleNotFoundException;
use JuniWalk\Tessa\Exceptions\BundleRecursionException;

final class BundleManager
{
	/** @var array<string, Bundle> */
	private array $bundles = [];

	public function __construct(
		private readonly Storage $storage,
	) {
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
	 * @return Asset[]
	 */
	public function compile(string $name, Type $type): iterable
	{
		$bundle = $this->getBundle($name);
		$this->detectRecursion($bundle);

		if ($extend = $bundle->getExtendBundle()) {
			yield from $this->compile($extend, $type);
		}

		foreach ($bundle->getAssets($type) as $asset) {
			$asset->setAttributes($bundle->getAttributes());

			if (!$asset->isModule() && !$bundle->isDirectLink()) {
				$asset = $this->storage->store($asset, $name.$type->value);
			}

			yield $asset;
		}
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
