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
		return (bool) preg_match('/\.scss$/i', $file);
	}


	public function getName(): string
	{
		return str_replace('.scss', '.css', parent::getName());
	}


	public function isTypeOf(string $type): bool
	{
		return in_array($type, ['css', 'scss']);
	}
}
