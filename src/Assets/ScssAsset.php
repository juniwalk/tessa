<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

final class ScssAsset extends FileAsset
{
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
