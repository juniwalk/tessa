<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\Asset;

final class ReadOnlyBundle implements Bundle
{
	/** @var string */
	protected $name;

	/** @var Asset[] */
	protected $assets;


	/**
	 * @param string   $name
	 * @param Asset[]  $assets
	 */
	public function __construct(string $name, Asset ... $assets)
	{
		$this->name = $name;
		$this->assets = $assets;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable
	{
		return $this->assets;
	}
}
