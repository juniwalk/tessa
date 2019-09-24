<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\Asset;

final class ReadOnlyBundle implements Bundle
{
    /** @var string */
    private $basePath;

	/** @var string */
	private $wwwDir;

	/** @var string */
	private $name;

	/** @var Asset[] */
	private $assets;


	/**
	 * @param string   $name
	 * @param Asset[]  $assets
	 */
	public function __construct(string $name, Asset ... $assets)
	{
		$this->name = $name;
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
		return str_replace($this->wwwDir, $this->basePath, $asset->getFile());
	}
}
