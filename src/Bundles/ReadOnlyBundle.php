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
	private $name;

	/** @var string */
	private $cookieConsent;

	/** @var string */
	private $basePath;

	/** @var string */
	private $wwwDir;

	/** @var bool */
	private $defer;

	/** @var Asset[] */
	private $assets;


	/**
	 * @param string  $name
	 * @param Asset[]  $assets
	 */
	public function __construct(string $name, ?Asset ... $assets)
	{
		$this->name = $name;
		$this->assets = $assets;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @param  string|NULL  $cookieConsent
	 * @return void
	 */
	public function setCookieConsent(?string $cookieConsent): void
	{
		$this->cookieConsent = $cookieConsent;
	}


	/**
	 * @return string|NULL
	 */
	public function getCookieConsent(): ?string
	{
		return $this->cookieConsent;
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
			$path .= '?'.$asset->getLastModified();
		}

		return $path;
	}
}
