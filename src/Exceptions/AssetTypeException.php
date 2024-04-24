<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

use JuniWalk\Tessa\Enums\Type;

final class AssetTypeException extends TessaException
{
	public static function fromFile(string $file): self
	{
		return new static('Failed to detect asset type from file '.$file, 500);
	}


	public static function fromType(mixed $type): self
	{
		if ($type instanceof Type) {
			$type = $type->name.'('.$type->value.')';
		}

		return new static('Type of "'.$type.'" is not supported for rendering.', 500);
	}
}
