<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

interface Bundle
{
	/**
	 * @return string
	 */
	public function getName(): string;


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable;
}
