<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

interface Asset
{
	/**
	 * @return string
	 */
	public function getFile(): string;


	/**
	 * @return string
	 */
	public function getContent(): string;
}
