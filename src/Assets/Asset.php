<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

interface Asset
{
	/**
	 * @param  string  $file
	 * @return bool
	 */
	public static function match(string $file): bool;


	/**
	 * @return string
	 */
	public function getFile(): string;


	/**
	 * @return string
	 */
	public function getContent(): string;


	/**
	 * @return string
	 */
	public function getName(): string;


	/**
	 * @return string
	 */
	public function getType(): string;


	/**
	 * @param  string  $type
	 * @return bool
	 */
	public function isTypeOf(string $type): bool;
}
