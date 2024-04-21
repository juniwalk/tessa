<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class HttpAsset extends AbstractAsset
{
	public static function match(string $file): bool
	{
		return (bool) preg_match('/^https?/i', $file);
	}


	public function getPath(): string
	{
		return $this->file;
	}


	public function getContent(): string|false
	{
		return file_get_contents($this->file);
	}
}
