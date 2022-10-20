<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Exceptions\UnableToDetectTypeException;

abstract class AbstractAsset implements Asset
{
	protected string $file;
	protected string $type;


	/**
	 * @throws UnableToDetectTypeException
	 */
	public function __construct(string $file, string $type = null)
	{
		if (!$type && !$type = pathinfo($file, PATHINFO_EXTENSION)) {
			throw UnableToDetectTypeException::fromFile($file);
		}

		$this->file = $file;
		$this->type = $type;
	}


	public function getFile(): string
	{
		return $this->file;
	}


	public function getName(): string
	{
		return basename($this->file);
	}


	public function getFolder(): string
	{
		return dirname($this->file);
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
		return hash_file('crc32b', $this->file);
	}


	abstract public function getContent(): string;


	public function hasBeenModified(string $file, bool $checkLastModified): bool
	{
		if (!file_exists($file)) {
			return true;
		}

		if (!$checkLastModified) {
			return false;
		}

		return filemtime($this->file) > filemtime($file);
	}
}
