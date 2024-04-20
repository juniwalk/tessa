<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Assets\FileAsset;
use JuniWalk\Tessa\Assets\HttpAsset;
use JuniWalk\Tessa\Exceptions\AssetStoringFailedException;
use JuniWalk\Tessa\Filter;

final class Storage
{
	private bool $checkLastModified = true;
	private array $filters = [];

	public function __construct(
		private string $outputDir,
		private bool $debugMode = false,
	) {
		$this->outputDir = rtrim($outputDir, '/');
	}


	public function setCheckLastModified(bool $checkLastModified = true): void
	{
		$this->checkLastModified = $checkLastModified;
	}


	public function hasCheckLastModified(): bool
	{
		return $this->checkLastModified;
	}


	public function setDebugMode(bool $debugMode = false): void
	{
		$this->debugMode = $debugMode;
	}


	public function isDebugMode(): bool
	{
		return $this->debugMode;
	}
	

	public function getOutputDir(): string
	{
		return $this->outputDir;
	}


	public function addFilter(Filter $filter): void
	{
		$this->filters[] = $filter;
	}


	/**
	 * @throws AssetStoringFailedException
	 */
	public function store(string $name, Asset $asset): Asset
	{
		$checkLastModified = $this->checkLastModified || $this->debugMode;
		$file = $this->outputDir.'/'.$name;

		if ($asset instanceof HttpAsset) {
			return $asset;
		}

		if (!$asset->hasBeenModified($file, $checkLastModified)) {
			return new FileAsset($file);
		}

		$content = $asset->getContent();

		foreach ($this->filters as $filter) {
			$content = $filter->apply($content, $asset);
		}

		if (file_put_contents($file, $content) === false) {
			throw AssetStoringFailedException::fromFile($file);
		}

		return new FileAsset($file);
	}
}
