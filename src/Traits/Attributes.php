<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Traits;

trait Attributes
{
	/** @var array<string, mixed> */
	protected array $attributes = [];

	/**
	 * @param array<string, mixed> $attributes
	 */
	public function setAttributes(array $attributes): void
	{
		$this->attributes = $attributes;
	}


	public function setAttribute(string $name, mixed $value): void
	{
		$this->attributes[$name] = $value;
	}


	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}


	public function getAttribute(string $name): mixed
	{
		return $this->attributes[$name] ?? null;
	}
}
