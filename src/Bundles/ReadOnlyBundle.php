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
	private ?string $cookieConsent;
	private string $basePath;
	private string $wwwDir;
	private bool $isModule = false;
	private bool $defer;
	private bool $async;

	/** @var Asset[] */
	private array $assets;

	public function __construct(
		private readonly string $name,
		Asset ...$assets,
	) {
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setModule(bool $module): void
	{
		$this->isModule = $module;
	}


	public function isModule(): bool
	{
		return $this->isModule;
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


	public function setExtendBundle(?string $extend): void {}
	public function getExtendBundle(): ?string {
		return null;
	}


	public function setAsync(bool $async = true): void
	{
		$this->async = $async;
	}


	public function isAsync(): bool
	{
		return $this->async;
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(): array
	{
		return $this->assets;
	}


	public function createPublicPath(Asset $asset): string
	{
		return str_replace($this->wwwDir, $this->basePath, $asset->getPath());
	}
}
