<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class HttpAsset extends FileAsset
{
	public static function match(string $file): bool
	{
		return str_starts_with($file, 'http');
	}


	public function getPath(): string
	{
		return $this->file;
	}
}
