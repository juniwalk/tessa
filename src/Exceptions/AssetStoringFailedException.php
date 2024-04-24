<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class AssetStoringFailedException extends TessaException
{
	public static function fromLastError(string $file): self
	{
		$lastError = error_get_last()['message'] ?? '';
		$message = 'Failed to store content of '.$file;

		return new static($message.' | '.$lastError, 500);
	}


	public static function fromOutputDir(string $dir): self
	{
		return new static('Failed to create output dir '.$dir, 500);
	}
}
