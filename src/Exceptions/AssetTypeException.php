<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class AssetTypeException extends TessaException
{
	public static function fromFile(string $file): self
	{
		return new static('Failed to detect asset type from file '.$file, 500);
	}


	public static function fromType(string $type): self
	{
		return new static('Unknown type of '.$type, 500);
	}
}
