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
	private string $type;

	/** @var array<string, mixed> */
	private array $attributes = [];

	/** @var Asset[] */
	private array $assets;

	public function __construct(
		private readonly string $name,
		Asset ...$assets,
	) {
		$this->type = pathinfo($name, PATHINFO_EXTENSION);
		$this->assets = $assets;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function setModule(bool $module): void
	{
		$this->setAttribute('type', $module ? 'module' : null);
	}


	public function isModule(): bool
	{
		return $this->getAttribute('type') === 'module';
	}


	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}


	public function setAttribute(string $name, mixed $value): void
	{
		$this->attributes[$name] = $value;
	}


	public function getAttribute(string $name): mixed
	{
		return $this->attributes[$name] ?? null;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getFile(): string
	{
		return $this->name.$this->type.'.'.$this->type;
	}


	public function getPath(): string
	{
		return $this->getFile();
	}


	public function getFolder(): string
	{
		return '';
	}


	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	public static function match(string $file): bool
	{
		return true;
	}


	public function getContent(): string|false
	{
		$content = '';

		foreach ($this->assets as $asset) {
			$content .= $asset->getContent().PHP_EOL;
		}

		return $content;
	}


	public function hasBeenModified(string $file, bool $checkLastModified): bool
	{
		foreach ($this->assets as $asset) {
			if (!$asset->hasBeenModified($file, $checkLastModified)) {
				continue;
			}

			return true;
		}

		return false;
	}


	public function setExtendBundle(?string $extend): void {}
	public function getExtendBundle(): ?string {
		return null;
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
		return [$this];
	}
}
