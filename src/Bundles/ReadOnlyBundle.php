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
	private string $name;
	private ?string $cookieConsent;
	private string $basePath;
	private string $wwwDir;
	private bool $defer;
	private bool $async;
	private array $assets;

	public function __construct(string $name, ?Asset ...$assets)
	{
		$this->name = $name;
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setCookieConsent(?string $cookieConsent): void
	{
		$this->cookieConsent = $cookieConsent;
	}


	public function getCookieConsent(): ?string
	{
		return $this->cookieConsent;
	}


	public function setBasePath(string $basePath): void
	{
		$this->basePath = $basePath;
	}


	public function setWwwDir(string $wwwDir): void
	{
		$this->wwwDir = $wwwDir;
	}


	public function setDeferred(bool $defer = true): void
	{
		$this->defer = $defer;
	}


	public function isDeferred(): bool
	{
		return $this->defer;
	}


	public function setAsync(bool $async = true): void
	{
		$this->async = $async;
	}


	public function isAsync(): bool
	{
		return $this->async;
	}


	public function getAssets(): array
	{
		return $this->assets;
	}


	public function createPublicPath(Asset $asset): string
	{
		$path = str_replace($this->wwwDir, $this->basePath, $asset->getFile());

		if ($asset instanceof FileAsset) {
			$path .= '?'.$asset->getLastModified();
		}

		return $path;
	}
}
