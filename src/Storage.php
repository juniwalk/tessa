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
	/** @var bool */
	private $checkLastModified = true;

	/** @var string */
	private $outputDir;

	/** @var Filter[] */
	private $filters = [];


	/**
	 * @param  string  $outputDir
	 */
	public function __construct(string $outputDir)
	{
		$this->outputDir = rtrim($outputDir, '/');
	}


	/**
	 * @param  bool  $checkLastModified
	 * @return void
	 */
	public function setCheckLastModified(bool $checkLastModified = true): void
	{
		$this->checkLastModified = $checkLastModified;
	}


	/**
	 * @return bool
	 */
	public function hasCheckLastModified(): bool
	{
		return $this->checkLastModified;
	}


	/**
	 * @return string
	 */
	public function getOutputDir(): string
	{
		return $this->outputDir;
	}


	/**
	 * @param  Filter  $filter
	 * @return void
	 */
	public function addFilter(Filter $filter): void
	{
		$this->filters[] = $filter;
	}


	/**
	 * @param  string  $name
	 * @param  Asset  $asset
	 * @return Asset
	 * @throws AssetStoringFailedException
	 */
	public function store(string $name, Asset $asset): Asset
	{
		$file = $this->outputDir.'/'.$name;

		if ($asset instanceof HttpAsset) {
			return $asset;
		}

		if (!$asset->hasBeenModified($file, $this->checkLastModified)) {
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
