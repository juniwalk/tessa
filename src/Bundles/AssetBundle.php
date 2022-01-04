<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets;

final class AssetBundle extends AbstractBundle
{
	/** @var bool */
	private $joinFiles = false;

	/** @var bool */
	private $defer = false;


	/**
	 * @param  bool  $joinFiles
	 * @return void
	 */
	public function setJoinFiles(bool $joinFiles = true): void
	{
		$this->joinFiles = $joinFiles;
	}


	/**
	 * @return bool
	 */
	public function isJoinFiles(): bool
	{
		return $this->joinFiles;
	}


	/**
	 * @param  bool  $defer
	 * @return void
	 */
	public function setDeferred(bool $defer = true): void
	{
		$this->defer = $defer;
	}


	/**
	 * @return bool
	 */
	public function isDeferred(): bool
	{
		return $this->defer;
	}


	/**
	 * @param  string[]  $files
	 * @return Asset[]
	 */
	public function discoverAsset(string $file): void
	{
		switch(true) {
			case Assets\HttpAsset::match($file):
				$asset = new Assets\HttpAsset($file);
				break;

			case Assets\ScssAsset::match($file):
				$asset = new Assets\ScssAsset($file);
				break;

			default:
				$asset = new Assets\FileAsset($file);
				break;
		}

		$this->assets[] = $asset;
	}


	/**
	 * @param  string  $type
	 * @return Bundle
	 */
	public function getCombinedBy(string $type): Bundle
	{
		$bundle = new AssetBundle($this->name);
		$bundle->setCookieConsent($this->cookieConsent);
		$bundle->setDeferred($this->defer);
		$name = $this->name.'.'.$type;
		$assets = [];

		foreach ($this->assets as $asset) {
			if (!$asset->isTypeOf($type)) {
				continue;
			}

			if ($asset instanceof Assets\HttpAsset) {
				$bundle->addAsset($asset);
				continue;
			}

			$assets[] = $asset;
		}

		$bundle->addAsset(new CombinedBundle($name, ... $assets));
		return $bundle;
	}
}
