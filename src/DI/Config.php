<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\DI;

class Config
{
	public string $outputDir;
	public bool $checkLastModified = true;
	public bool $directLink = false;
	public bool $debugMode = false;

	/** @var string[] */
	public array $filters = [];

	/** @var Bundle[] */
	public array $bundles = [];


	public function __set(string $name, Bundle $bundle): void
	{
		$this->bundles[$name] = $bundle;
	}
}
