<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Assets\FileAsset;
use JuniWalk\Tessa\Bundle;

final class ReadOnlyBundle implements Bundle
{
	/** @var string */
	private $basePath;

	/** @var string */
	private $wwwDir;

	/** @var string */
	private $name;

	/** @var bool */
	private $defer;

	/** @var Asset[] */
	private $assets;


	/**
	 * @param string   $name
	 * @param bool  $defer
	 * @param Asset[]  $assets
	 */
	public function __construct(string $name, bool $defer, ?Asset ... $assets)
	{
		$this->name = $name;
		$this->defer = $defer;
		$this->assets = $assets;
	}


	/**
	 * @param  string  $basePath
	 * @return void
	 */
	public function setBasePath(string $basePath): void
	{
		$this->basePath = $basePath;
	}


	/**
	 * @param  string  $wwwDir
	 * @return void
	 */
	public function setWwwDir(string $wwwDir): void
	{
		$this->wwwDir = $wwwDir;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return bool
	 */
	public function isDeferred(): bool
	{
		return $this->defer;
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable
	{
		return $this->assets;
	}


	/**
	 * @param  Asset  $asset
	 * @return string
	 */
	public function createPublicPath(Asset $asset): string
	{
		$path = str_replace($this->wwwDir, $this->basePath, $asset->getFile());

		if ($asset instanceof FileAsset) {
			$file .= '?'.$asset->getLastModified();
		}

		return $path;
	}
}
