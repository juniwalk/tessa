<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

class FileAsset extends AbstractAsset
{
	public static function match(string $file): bool
	{
		return true;
	}


	public function getPath(): string
	{
		return $this->file.$this->getLastModified();
	}


	public function getContent(): string|false
	{
		return file_get_contents($this->file) ?: '';
	}


	private function getLastModified(): ?string
	{
		if (!$lastModified = @filemtime($this->file)) {
			return null;
		}

		return '?'.$lastModified;
	}
}
