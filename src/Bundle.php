<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

interface Bundle
{
	public function getName(): string;
	public function setCookieConsent(?string $cookieConsent): void;
	public function getCookieConsent(): ?string;
	public function setDeferred(bool $defer = true): void;
	public function isDeferred(): bool;
	public function setExtendBundle(?string $extend): void;
	public function getExtendBundle(): ?string;
	public function setAsync(bool $async = true): void;
	public function isAsync(): bool;

	/**
	 * @return Asset[]
	 */
	public function getAssets(): array;
}
