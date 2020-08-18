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
