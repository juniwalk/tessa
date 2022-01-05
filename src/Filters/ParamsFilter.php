<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use Nette\DI\Helpers;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

final class ParamsFilter implements Filter
{
    /** @var string */
    const PATTERN = '/(\%([A-Za-z][A-Za-z0-9\.]*[A-Za-z0-9])\%)/';

	/** @var string[] */
	private $params;


	/**
	 * @param string[]  $params
	 */
	public function __construct(iterable $params = [])
	{
		$this->params = $params;
	}


	/**
	 * @param  string  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function setParam(string $key, $value): void
	{
		$this->params[$key] = $value;
	}


	/**
	 * @param  string  $content
	 * @param  Asset  $asset
	 * @return string
	 */
	public function apply(string $content, Asset $asset): string
	{
		if (!$asset->isTypeOf('js')) {
			return $content;
		}

		$vars = Strings::matchAll($content, static::PATTERN);

		foreach ($vars as $var) {
			try {
				$value = Helpers::expand($var[0], $this->params, true);

			} catch (InvalidArgumentException $e) {
				continue;
			}

			$content = str_replace($var[0], json_encode($value), $content);
		}

		return $content;
	}
}
