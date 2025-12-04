<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Assets;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Traits\Attributes;

class Module implements Asset
{
	use Attributes;

	protected bool $isModule = true;
	protected string $name;
	protected string $file;

	public function __construct(string $name, string $file)
	{
		$this->file = $file;
		$this->name = $name;
	}


	public static function match(string $file): bool
	{
		return is_file($file);
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getPath(): string
	{
		return $this->file;
	}


	public function getFile(): string
	{
		return $this->file;
	}


	public function getExt(): string
	{
		throw new \Exception('Not implemented');
	}


	public function getContent(): string
	{
		throw new \Exception('Not implemented');
	}


	public function setModule(bool $module): void
	{
		$this->isModule = $module;
	}


	public function isModule(): bool
	{
		return $this->isModule;
	}
}
