<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Bundles\ReadOnlyBundle;
use JuniWalk\Tessa\Exceptions\BundleNotFoundException;
use JuniWalk\Tessa\Exceptions\BundleRecursionException;
use JuniWalk\Tessa\Exceptions\ReadOnlyBundleException;
use Nette\Http\IRequest;

final class BundleManager
{
	private string $basePath;
	private bool $directLinking = false;

	/** @var array<string, Bundle> */
	private array $bundles = [];

	public function __construct(
		private string $wwwDir,
		private Storage $storage,
		IRequest $httpRequest,
	) {
		$this->basePath = $httpRequest->getUrl()->getBasePath();
		$this->wwwDir = rtrim($wwwDir, '/').'/';
	}


	public function setDirectLinking(bool $directLinking): void
	{
		$this->directLinking = $directLinking;
	}


	public function isDirectLinking(): bool
	{
		return $this->directLinking;
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
	 * @throws ReadOnlyBundleException
	 */
	public function compile(string $bundleName, string $type): Bundle
	{
		$bundle = $this->getBundle($bundleName);
		$bundleName = $bundleName.$type;
		$assets = [];

		if ($bundle instanceof ReadOnlyBundle) {
			throw ReadOnlyBundleException::fromBundle($bundle);
		}

		$this->detectRecursion($bundle);

		if ($bundle instanceof AssetBundle && $bundle->isJoinFiles()) {
			$bundle = $bundle->getCombinedBy($type);
		}

		if ($extend = $bundle->getExtendBundle()) {
			$assets = $this->compile($extend, $type)->getAssets();
		}

		foreach ($bundle->getAssets() as $asset) {
			if (!$asset->isTypeOf($type)) {
				continue;
			}

			if (!$this->directLinking && !$asset->isModule() && !$bundle->isModule()) {
				$asset = $this->storage->store($asset, $bundleName);
			}

			$assets[] = $asset;
		}

		$output = new ReadOnlyBundle($bundleName, ...$assets);
		$output->setCookieConsent($bundle->getCookieConsent());
		$output->setDeferred($bundle->isDeferred());
		$output->setModule($bundle->isModule());
		$output->setAsync($bundle->isAsync());
		$output->setBasePath($this->basePath);
		$output->setWwwDir($this->wwwDir);

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
			if (isset($history[$extend])) {
				throw BundleRecursionException::fromBundle($bundle, $extend);
			}

			$history = $this->detectRecursion(
				$this->getBundle($extend),
				$history
			);
		}

		return $history;
	}
}
