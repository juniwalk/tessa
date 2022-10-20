<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

use JuniWalk\Tessa\Bundles\Bundle;

final class ReadOnlyBundleException extends TessaException
{
	public static function fromBundle(Bundle $bundle): self
	{
		return new static('Bundle "'.$bundle->getName().'" is read-only and cannot be compiled.');
	}
}
