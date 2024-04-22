<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Bundle;

abstract class AbstractBundle implements Bundle
{
	/** @var array<string, mixed> */
	protected array $attributes = [];

	protected ?string $extend = null;

	/** @var Asset[] */
	protected array $assets;

	public function __construct(
		protected readonly string $name,
		Asset ...$assets,
	) {
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setExtendBundle(?string $extend): void
	{
		$this->extend = $extend;
	}


	public function getExtendBundle(): ?string
	{
		return $this->extend;
	}


	public function setAttribute(string $name, mixed $value): void
	{
		$this->attributes[$name] = $value;
	}


	public function getAttribute(string $name): mixed
	{
		return $this->attributes[$name] ?? null;
	}


	/**
	 * @param array<string, mixed> $attributes
	 */
	public function setAttributes(array $attributes): void
	{
		$this->attributes = $attributes;
	}


	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}


	public function addAsset(Asset $asset): void
	{
		$this->assets[] = $asset;
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(): array
	{
		return $this->assets;
	}
}
