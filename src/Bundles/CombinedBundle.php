<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Bundle;

final class CombinedBundle implements Asset, Bundle
{
	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var Asset[] */
	private $assets;


	/**
	 * @param string   $name
	 * @param Asset[]  $assets
	 */
	public function __construct(string $name, ?Asset ... $assets)
	{
		$this->type = pathinfo($name, PATHINFO_EXTENSION);
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
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}


	/**
	 * @param  string  $type
	 * @return bool
	 */
	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	/**
	 * @return string
	 */
	public function getCrc32(): string
	{
		return hash('crc32b', $this->getContent());
	}


	/**
	 * @return string
	 */
	public function getContent(): string
	{
		$content = '';

		foreach ($this->assets as $asset) {
			$content .= $asset->getContent().PHP_EOL;
		}

		return $content;
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
		return [$this];
	}
}
