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
	public function getExt(): string;

	public function getFile(): string;
	public function getPath(): string;

	public function setModule(bool $module): void;
	public function isModule(): bool;

	public function getContent(): string;
}
