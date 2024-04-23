<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Exceptions\AssetContentException;
use JuniWalk\Tessa\Exceptions\AssetTypeException;

class FileAsset implements Asset
{
	protected string $file;
	protected string $type;
	protected bool $isModule = false;

	/**
	 * @throws AssetTypeException
	 */
	public function __construct(string $file, ?string $type = null)
	{
		if (!$type && !$type = pathinfo($file, PATHINFO_EXTENSION)) {
			throw AssetTypeException::fromFile($file);
		}

		if ($type === 'mjs') {
			$this->isModule = true;
			$type = 'js';
		}

		$this->file = $file;
		$this->type = $type;
	}


	public static function match(string $file): bool
	{
		return is_file($file);
	}


	public function getName(): string
	{
		return basename($this->file);
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getFile(): string
	{
		return $this->file;
	}


	public function getPath(): string
	{
		return $this->file.$this->lastModified();
	}


	public function setModule(bool $module): void
	{
		$this->isModule = $module;
	}


	public function isModule(): bool
	{
		return $this->isModule;
	}


	/**
	 * @throws AssetContentException
	 */
	public function getContent(): string
	{
		if (!$content = @file_get_contents($this->file)) {
			throw AssetContentException::fromLastError($this->file);
		}

		return $content;
	}


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


	protected function lastModified(): ?string
	{
		if (!$lastModified = @filemtime($this->file)) {
			return null;
		}

		return '?'.$lastModified;
	}
}
