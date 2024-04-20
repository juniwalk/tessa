<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use JuniWalk\Utils\Strings;
use Nette\DI\Helpers;
use Nette\InvalidArgumentException;

final class ParamsFilter implements Filter
{
    private const Pattern = '/(\%([A-Za-z][A-Za-z0-9\.]*[A-Za-z0-9])\%)/';

	public function __construct(
		private array $params = [],
	) {
	}


	public function setParam(string $key, mixed $value): void
	{
		$this->params[$key] = $value;
	}


	public function apply(string $content, Asset $asset): string
	{
		if (!$asset->isTypeOf('js')) {
			return $content;
		}

		$vars = Strings::matchAll($content, static::Pattern);

		foreach ($vars as $var) {
			try {
				$value = Helpers::expand($var[0], $this->params, true);

			} catch (InvalidArgumentException) {
				continue;
			}

			$content = str_replace($var[0], json_encode($value), $content);
		}

		return $content;
	}
}
