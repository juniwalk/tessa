<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

use JuniWalk\Tessa\Bundles\Bundle;

final class BundleRecursionException extends TessaException
{
	/**
	 * @param  Bundle  $bundle
	 * @param  string  $extend
	 * @return self
	 */
	static public function fromBundle(Bundle $bundle, string $extend): self
	{
		return new self('Bundle "'.$bundle->getName().'" has recursion with bundle "'.$extend.'".', 500);
	}
}
