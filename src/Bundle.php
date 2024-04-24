<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Enums\Type;

interface Bundle
{
	public function getName(): string;
	public function setDirectLink(bool $directLink): void;
	public function isDirectLink(): bool;
	public function setExtendBundle(?string $extend): void;
	public function getExtendBundle(): ?string;

	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array;
	public function setAttribute(string $name, mixed $value): void;
	public function getAttribute(string $name): mixed;

	/**
	 * @return Asset[]
	 */
	public function getAssets(?Type $type = null): array;
	public function addAsset(Asset $asset): void;
}
