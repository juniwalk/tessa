<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class ScssAsset extends FileAsset
{
	public static function match(string $file): bool
	{
		if (!parent::match($file)) {
			return false;
		}

		return str_ends_with($file, '.scss');
	}


	public function getName(): string
	{
		return str_replace('.scss', '.css', parent::getName());
	}
}
