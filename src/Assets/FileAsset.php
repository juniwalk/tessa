<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class FileAsset extends AbstractAsset
{
	/**
	 * @param  string  $file
	 * @return bool
	 */
	public static function match(string $file): bool
	{
		return true;
	}


	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->file;
	}


	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return file_get_contents($this->file);
	}


	/**
	 * @return int
	 */
	public function getLastModified(): int
	{
		return filemtime($this->file);
	}
}
