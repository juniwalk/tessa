<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Assets\FileAsset;
use JuniWalk\Tessa\Assets\HttpAsset;
use JuniWalk\Tessa\Assets\ScssAsset;
use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\Exceptions\AssetTypeException;

class AssetBundle implements Bundle
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
	 * @throws AssetTypeException
	 */
	public function addAssetFrom(string $file): void
	{
		// TODO: Detect type using schema? Example: js://, mjs://, css://
		// TODO: Remote files will have double schema: mjs://https://cdn.io/jquery-v4.0.0.js
		$params = $this->parseParams($file);
		$type = $params['type'] ?? null;

		if (!is_string($type)) {
			$type = null;
		}

		$this->assets[] = match (true) {
			ScssAsset::match($file) => new ScssAsset($file, $type),
			HttpAsset::match($file) => new HttpAsset($file, $type),
			FileAsset::match($file) => new FileAsset($file, $type),

			default => throw AssetTypeException::fromFile($file),
		};
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(?string $type = null): array
	{
		return array_filter($this->assets, fn($x) => !$type || $x->isTypeOf($type));
	}


	/**
	 * @return array<int|string, mixed>
	 */
	private function parseParams(string $file): array
	{
		if (!$query = parse_url($file, PHP_URL_QUERY)) {
			return [];
		}

		parse_str($query, $params);
		return $params;
	}
}
