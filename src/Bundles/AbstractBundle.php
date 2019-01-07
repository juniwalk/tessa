<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\Asset;

abstract class AbstractBundle implements Bundle
{
	/** @var string */
	protected $name;

	/** @var string */
	protected $extend;

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
	 * @param  string|NULL  $extend
	 * @return void
	 */
	public function setExtendBundle(?string $extend): void
	{
		$this->extend = $extend;
	}


	/**
	 * @return string|NULL
	 */
	public function getExtendBundle(): ?string
	{
		return $this->extend;
	}


	/**
	 * @param  Asset  $asset
	 * @return void
	 */
	public function addAsset(Asset $asset): void
	{
		$this->assets[] = $asset;
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(): iterable
	{
		return $this->assets;
	}
}
