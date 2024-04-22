<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Exceptions\AssetTypeException;

abstract class AbstractAsset implements Asset
{
	protected bool $isModule = false;

	/**
	 * @throws AssetTypeException
	 */
	public function __construct(
		protected string $file,
		protected ?string $type = null,
	) {
		if (!$type && !$type = pathinfo($file, PATHINFO_EXTENSION)) {
			throw AssetTypeException::fromFile($file);
		}

		if ($type === 'mjs') {
			$this->isModule = true;
			$type = 'js';
		}

		$this->type = $type;
	}


	public function getFile(): string
	{
		return $this->file;
	}


	public function getPath(): string
	{
		return $this->file;
	}


	public function getName(): string
	{
		return basename($this->file);
	}


	public function getType(): string
	{
		return $this->type ?? '';
	}


	public function setModule(bool $module): void
	{
		$this->isModule = $module;
	}


	public function isModule(): bool
	{
		return $this->isModule;
	}


	abstract public function getContent(): string|false;


	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	public function isOutOfDate(string $file, bool $checkLastModified): bool
	{
		if (!$checkLastModified) {
			return false;
		}

		if (!file_exists($file)) {
			return true;
		}

		return filemtime($this->file) > filemtime($file);
	}
}
