<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Bundles\Bundle;
use JuniWalk\Tessa\Bundles\ReadOnlyBundle;
use JuniWalk\Tessa\Exceptions\BundleNotFoundException;
use JuniWalk\Tessa\Exceptions\ReadOnlyBundleException;
use Nette\Http\IRequest;

final class BundleManager
{
	/** @var Storage */
	private $storage;

    /** @var string */
    private $basePath;

	/** @var string */
	private $wwwDir;

	/** @var Bundle[] */
	private $bundles = [];


	/**
	 * @param  string  $wwwDir
	 * @param  IRequest  $httpRequest
	 * @param  Storage  $storage
	 */
	public function __construct(
		string $wwwDir,
		IRequest $httpRequest,
		Storage $storage
	) {
        $this->basePath = $httpRequest->getUrl()->getBasePath();
		$this->wwwDir = $wwwDir.'/';
		$this->storage = $storage;
	}


	/**
	 * @param  Bundle  $bundle
	 * @return void
	 */
	public function addBundle(Bundle $bundle): void
	{
		$this->bundles[$bundle->getName()] = $bundle;
	}


	/**
	 * @param  string  $bundle
	 * @return Bundle
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
	 * @param  string  $bundle
	 * @param  string  $type
	 * @throws BundleNotFoundException
	 * @throws ReadOnlyBundleException
	 * @return Bundle
	 */
	public function compile(string $bundle, string $type): Bundle
	{
		$bundle = $this->getBundle($bundle);
		$assets = [];

		if ($bundle instanceof ReadOnlyBundle) {
			throw ReadOnlyBundleException::fromBundle($bundle);
		}

		if ($bundle instanceof AssetBundle && $bundle->isJoinFiles()) {
			$bundle = $bundle->getCombinedBy($type);
		}

		if ($extend = $bundle->getExtendBundle()) {
			// TODO Make sure there is no recursion in bundle compilation.
			$assets += $this->compile($extend, $type)->getAssets();
		}

		foreach ($bundle->getAssets() as $asset) {
			if (!$asset->isTypeOf($type)) {
				continue;
			}

			$name = implode('-', array_filter([
				$bundle->getName().$type,
				// TODO Determine how to lower time hit on this
				// TODO Create cache of hash($oldPath) => $crc32b
				//$asset->getCrc32(),
				$asset->getName(),
			]));

			$assets[] = $this->storage->store($name, $asset);
		}

		$output = new ReadOnlyBundle($bundle->getName().$type, ... $assets);
		$output->setBasePath($this->basePath);
		$output->setWwwDir($this->wwwDir);

		return $output;
	}
}
