<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Exceptions\AssetContentException;

class FileAsset implements Asset
{
	protected string $file;
	protected string $ext;
	protected bool $isModule = false;

	public function __construct(string $file, string $ext)
	{
		if ($ext === 'mjs') {
			$this->isModule = true;
			$ext = 'js';
		}

		$this->file = $file;
		$this->ext = $ext;
	}


	public static function match(string $file): bool
	{
		return is_file($file);
	}


	public function getName(): string
	{
		return basename($this->file);
	}


	public function getFile(): string
	{
		return $this->file;
	}


	public function getPath(): string
	{
		return $this->file.$this->lastModified();
	}


	public function getExt(): string
	{
		return $this->ext;
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


	public function setModule(bool $module): void
	{
		$this->isModule = $module;
	}


	public function isModule(): bool
	{
		return $this->isModule;
	}


	protected function lastModified(): ?string
	{
		if (!$lastModified = @filemtime($this->file)) {
			return null;
		}

		return '?'.$lastModified;
	}
}
