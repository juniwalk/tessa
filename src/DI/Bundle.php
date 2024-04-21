<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\DI;

class Bundle
{
	public ?string $cookieConsent = null;
	public ?string $extend= null;
	public bool $joinFiles = false;
	public bool $defer = false;
	public bool $async = false;

	/** @var string[] */
	public array $assets = [];
}
