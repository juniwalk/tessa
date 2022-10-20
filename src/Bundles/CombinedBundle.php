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
	private string $name;
	private string $type;
	private array $assets;


	public function __construct(string $name, ?Asset ... $assets)
	{
		$this->type = pathinfo($name, PATHINFO_EXTENSION);
		$this->name = $name;
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	public function getCrc32(): string
	{
		return hash('crc32b', $this->getContent());
	}


	public function getContent(): string
	{
		$content = '';

		foreach ($this->assets as $asset) {
			$content .= $asset->getContent().PHP_EOL;
		}

		return $content;
	}


	public function addAsset(Asset $asset): void
	{
		$this->assets[] = $asset;
	}


	public function getAssets(): iterable
	{
		return [$this];
	}
}
