<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class AssetContentException extends TessaException
{
	public static function fromLastError(string $file): self
	{
		$lastError = error_get_last()['message'] ?? '';
		$message = 'Failed to fetch content of '.$file;

		return new static($message.' | '.$lastError, 500);
	}
}
