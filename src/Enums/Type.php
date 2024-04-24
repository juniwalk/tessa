<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Enums;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use JuniWalk\Utils\Enums\Traits\Labeled;

enum Type: string
{
	use Labeled;

	case JavaScript = 'js';
	case StyleSheet = 'css';
	case Unknown = '';


	/**
	 * @throws AssetTypeException
	 */
	public static function fromExtension(string $ext): ?static
	{
		foreach (self::cases() as $type) {
			if (!$type->supports($ext)) {
				continue;
			}

			return $type;
		}

		return null;
	}


	public function supports(Asset|string $ext): bool
	{
		if ($ext instanceof Asset) {
			$ext = $ext->getExt();
		}

		return in_array($ext, match ($this) {
			self::JavaScript => ['js', 'mjs'],
			self::StyleSheet => ['css', 'scss'],

			default => [],
		});
	}
}
