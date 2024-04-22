<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Asset;

final class CombinedBundle extends AbstractBundle implements Asset
{
	private string $type;

	public function __construct(
		protected readonly string $name,
		Asset ...$assets,
	) {
		$this->type = pathinfo($name, PATHINFO_EXTENSION);
		$this->assets = $assets;
	}


	public function setExtendBundle(?string $extend): void {}
	public function getExtendBundle(): ?string { return null; }


	public function setModule(bool $module): void
	{
		$this->setAttribute('type', $module ? 'module' : null);
	}


	public function isModule(): bool
	{
		return $this->getAttribute('type') === 'module';
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


	public function isOutOfDate(string $file, bool $checkLastModified): bool
	{
		foreach ($this->assets as $asset) {
			if (!$asset->isOutOfDate($file, $checkLastModified)) {
				continue;
			}

			return true;
		}

		return false;
	}
}
