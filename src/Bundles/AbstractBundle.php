<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Bundle;

abstract class AbstractBundle implements Bundle
{
	protected ?string $cookieConsent;
	protected ?string $extend;
	protected array $assets;

	public function __construct(
		protected readonly string $name,
		?Asset ...$assets,
	) {
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function isDeferred(): bool
	{
		return false;
	}


	public function setExtendBundle(?string $extend): void
	{
		$this->extend = $extend;
	}


	public function getExtendBundle(): ?string
	{
		return $this->extend;
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
		return $this->assets;
	}
}
