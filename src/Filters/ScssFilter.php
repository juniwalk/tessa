<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use InvalidArgumentException;
use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use JuniWalk\Tessa\Exceptions\MissingOptionalFeatureException;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter;

final class ScssFilter implements Filter
{
	private Compiler $scss;

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
	 * @throws InvalidArgumentException
	 */
	public function setFormatter(string $formatter): void
	{
		if (!is_subclass_of($formatter, Formatter::class, true)) {
			throw new InvalidArgumentException('Expected name of subclass of '.Formatter::class);
		}

		$this->scss->setFormatter($formatter);
	}


	public function addImportPath(string $path): void
	{
		$this->scss->addImportPath($path);
	}


	/**
	 * @param mixed[] $variables
	 */
	public function setVariables(array $variables): void
	{
		$this->scss->setVariables($variables);
	}


	public function apply(string|false $content, Asset $asset): string
	{
		if (!$content || !$asset->isTypeOf('scss')) {
			return $content ?: '';
		}

		return $this->scss->compile($content);
	}
}
