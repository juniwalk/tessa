<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

abstract class AbstractAsset implements Asset
{
	/** @var string */
	protected $file;

	/** @var string */
	protected $type;


	/**
	 * @param string  $file
	 */
	public function __construct(string $file)
	{
		$this->type = pathinfo($file, PATHINFO_EXTENSION);
		$this->file = $file;
	}


	/**
	 * @return string
	 */
	public function getFolder(): string
	{
		return dirname($this->file);
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return basename($this->file);
	}


	/**
	 * @return string
	 */
	public function getFile(): string
	{
		return $this->file;
	}


	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}


	/**
	 * @param  string  $type
	 * @return bool
	 */
	public function isTypeOf(string $type): bool
	{
		return $this->type == $type;
	}


	/**
	 * @return string
	 */
	public function getCrc32(): string
	{
		return hash_file('crc32b', $this->file);
	}


	/**
	 * @return string
	 */
	abstract public function getContent(): string;


	/**
	 * @param  string  $file
	 * @param  bool  $checkLastModified
	 * @return bool
	 */
	public function hasBeenModified(string $file, bool $checkLastModified): bool
	{
		if (!file_exists($file)) {
			return TRUE;
		}

		if (!$checkLastModified) {
			return FALSE;
		}

		return filemtime($this->file) > filemtime($file);
	}
}
