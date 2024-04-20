<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Bundle;

final class CombinedBundle implements Asset, Bundle
{
	private string $type;
	private array $assets;
	private ?string $cookieConsent = null;
	private bool $defer = false;
	private bool $async = false;

	public function __construct(
		private readonly string $name,
		?Asset ...$assets,
	) {
		$this->type = pathinfo($name, PATHINFO_EXTENSION);
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getFile(): string
	{
		return $this->name.$this->type.'.'.$this->type;
	}


	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	public static function match(string $file): bool
	{
		return true;
	}


	public function getCrc32(): string
	{
		return hash('crc32b', $this->getContent());
	}


	public function getContent(): string
	{
		$content = '';

		foreach ($this->assets as $asset) {
			$content .= $asset->getContent().PHP_EOL;
		}

		return $content;
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


	public function setCookieConsent(?string $cookieConsent): void
	{
		$this->cookieConsent = $cookieConsent;
	}


	public function getCookieConsent(): ?string
	{
		return $this->cookieConsent;
	}


	public function addAsset(Asset $asset): void
	{
		$this->assets[] = $asset;
	}


	public function getAssets(): array
	{
		return [$this];
	}
}
