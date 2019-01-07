<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

final class HttpAsset extends AbstractAsset
{
	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->file;
	}


	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return file_get_contents($this->file);
	}
}
