<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

interface Asset
{
	public static function match(string $file): bool;
	public function getFile(): string;
	public function getContent(): string;
	public function getName(): string;
	public function getType(): string;
	public function isTypeOf(string $type): bool;
}
