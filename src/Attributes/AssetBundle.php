<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AssetBundle
{
	public function __construct(
		public readonly string $bundleName,
		public readonly bool $clearPrevious = false,
	) {
	}
}
