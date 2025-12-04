<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

class Module
{
	public function __construct(
		protected string $name,
		protected string $path,
	) {
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getPath(): string
	{
		return $this->path;
	}
}
