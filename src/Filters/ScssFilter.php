<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use JuniWalk\Tessa\Exceptions\MissingOptionalFeatureException;
use ScssPhp\ScssPhp\Compiler;

final class ScssFilter implements Filter
{
	/** @var Compiler */
	private $scss;


	/**
	 * @throws MissingOptionalFeatureException
	 */
	public function __construct()
	{
		if (!class_exists(Compiler::class, true)) {
			throw MissingOptionalFeatureException::fromFeature('scss', 'scssphp/scssphp');
		}

		$this->scss = new Compiler;
	}


	/**
	 * @param  string[]  $formatter
	 * @return void
	 */
	public function setFormatter(string $formatter): void
	{
		$this->scss->setFormatter($formatter);
	}


	/**
	 * @param  string  $path
	 * @return void
	 */
	public function addImportPath(string $path): void
	{
		$this->scss->addImportPath($path);
	}


	/**
	 * @param  string[]  $variables
	 * @return void
	 */
	public function setVariables(array $variables): void
	{
		$this->scss->setVariables($variables);
	}


	/**
	 * @param  string  $content
	 * @param  Asset  $asset
	 * @return string
	 */
	public function apply(string $content, Asset $asset): string
	{
		if (!$asset->isTypeOf('scss')) {
			return $content;
		}

		return $this->scss->compile($content);
	}
}
