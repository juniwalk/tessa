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
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use JuniWalk\Utils\Strings;

class AssetBundle implements Bundle
{
	protected ?string $extend = null;

	/** @var array<string, mixed> */
	protected array $attributes = [];

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
	public function addAssetFrom(string $file, ?string $ext = null): void
	{
		$ext ??= $this->discoverExtension($file);

		if (str_starts_with($file, $ext.'://')) {
			$file = substr($file, strlen($ext) +3);
		}

		$this->assets[] = match (true) {
			ScssAsset::match($file) => new ScssAsset($file, $ext),
			HttpAsset::match($file) => new HttpAsset($file, $ext),
			FileAsset::match($file) => new FileAsset($file, $ext),

			default => throw AssetTypeException::fromFile($file),
		};
	}


	/**
	 * @return Asset[]
	 */
	public function getAssets(?Type $type = null): array
	{
		return array_filter($this->assets, fn($x) => !$type || $type->supports($x));
	}


	/**
	 * @throws AssetTypeException
	 */
	protected function discoverExtension(string $file): string
	{
		$scheme = Strings::match($file, '/^([a-z]+):\/\//i')[1] ?? null;

		if ($scheme && Type::fromExtension($scheme)) {
			return $scheme;
		}

		if ($scheme && $position = strpos($file, '?')) {
			$file = substr($file, 0, $position);
		}

		if ($type = pathinfo($file, PATHINFO_EXTENSION)) {
			return $type;
		}

		throw AssetTypeException::fromFile($file);
	}
}
