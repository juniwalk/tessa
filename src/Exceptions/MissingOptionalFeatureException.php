<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class MissingOptionalFeatureException extends TessaException
{
	public static function fromFeature(string $feature, string $package): self
	{
		return new static('For use of '.$feature.' you need to install '.$package, 500);
	}
}
