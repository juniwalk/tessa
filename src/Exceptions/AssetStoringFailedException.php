<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class AssetStoringFailedException extends TessaException
{
	public static function fromFile(string $file): self
	{
		return new static('Failed to store asset to file '.$file, 500);
	}
}
