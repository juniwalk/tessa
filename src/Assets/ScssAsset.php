<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class ScssAsset extends FileAsset
{
	/**
	 * @param  string  $file
	 * @return bool
	 */
	public static function match(string $file): bool
	{
		return (bool) preg_match('/\.scss$/i', $file);
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return str_replace('.scss', '.css', parent::getName());
	}


	/**
	 * @param  string  $type
	 * @return bool
	 */
	public function isTypeOf(string $type): bool
	{
		return $this->type === 'scss' && $type === 'css';
	}
}
