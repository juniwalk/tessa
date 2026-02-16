<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\MissingOptionalFeatureException;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Value\Value;

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


	public function addImportPath(string $path): void
	{
		$this->scss->addImportPath($path);
	}


	/**
	 * @param array<string, Value> $variables
	 */
	public function setVariables(array $variables): void
	{
		$this->scss->addVariables($variables);
	}


	public function apply(string $content, Asset $asset): string
	{
		if (!$content || !Type::StyleSheet->supports($asset)) {
			return $content;
		}

		$result = $this->scss->compileString($content);

		return $result->getCss();
	}
}
