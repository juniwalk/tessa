<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

interface Bundle
{
	/**
	 * @return string
	 */
	public function getName(): string;


	/**
	 * @param  string|null  $cookieConsent
	 * @return void
	 */
	public function setCookieConsent(?string $cookieConsent): void;


	/**
	 * @return string|null
	 */
	public function getCookieConsent(): ?string;


	/**
	 * @param  bool  $defer
	 * @return void
	 */
	public function setDeferred(bool $defer = true): void;


	/**
	 * @return bool
	 */
	public function isDeferred(): bool;


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable;
}
