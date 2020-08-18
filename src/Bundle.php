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
	 * @return bool
	 */
	public function isDeferred(): bool;


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable;
}
