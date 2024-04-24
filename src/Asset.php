<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

interface Asset
{
	public static function match(string $file): bool;

	public function getName(): string;
	public function getPath(): string;
	public function getFile(): string;
	public function getExt(): string;
	public function getContent(): string;

	public function setModule(bool $module): void;
	public function isModule(): bool;

	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array;
	public function getAttribute(string $name): mixed;

	/**
	 * @param array<string, mixed> $attributes
	 */
	public function setAttributes(array $attributes): void;
}
