<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Asset;
use JuniWalk\Tessa\Filter;
use JuniWalk\Utils\Strings;
use Nette\Http\IRequest;

final class UrlFixerFilter implements Filter
{
	/**
	 * @author Kravko
	 * @see https://github.com/janmarek/WebLoader/blob/2dad67556ab2f434bbb14de048ce539155a6e1df/WebLoader/Filter/CssUrlsFilter.php
	 */
	const PATTERN = '~
		(?<![a-z])
		url\(                                     ## url(
			\s*                                   ##   optional whitespace
			([\'"])?                              ##   optional single/double quote
			(?!data:)                             ##   keep data URIs
			(   (?: (?:\\\\.)+                    ##     escape sequences
				|   [^\'"\\\\,()\s]+              ##     safe characters
				|   (?(1)   (?!\1)[\'"\\\\,() \t] ##       allowed special characters
					|       ^                     ##       (none, if not quoted)
					)
				)*                                ##     (greedy match)
			)
			(?(1)\1)                              ##   optional single/double quote
			\s*                                   ##   optional whitespace
		\)                                        ## )
	~xs';

	private string $docRoot;
	private string $basePath;

	public function __construct(string $docRoot, IRequest $http)
	{
		$this->basePath = $http->getUrl()->getBasePath();
		$this->docRoot = $docRoot;
	}


	public function apply(string $content, Asset $asset): string
	{
		if (!$asset->isTypeOf('css')) {
			return $content;
		}

		$urls = Strings::matchAll($content, static::PATTERN);

		foreach ($urls as $url) {
			$absoluteUrl = $this->absolutizeUrl($url[2], $asset);
			$absoluteUrl = 'url('.$url[1].$absoluteUrl.$url[1].')';

			$content = str_replace($url[0], $absoluteUrl, $content);
		}

		return $content;
	}


	public function absolutizeUrl(string $url, Asset $asset): string
	{
		if (preg_match('/^([a-z]+:|\/)/i', $url)) {
			return $url;
		}

		$path = $asset->getFolder();

		if (strncmp($path, $this->docRoot, strlen($this->docRoot)) === 0) {
			$path = $this->basePath.str_replace($this->docRoot, '', $path).'/'.$url;

		} else {
			return $url;
		}

		return $this->cannonicalizePath($path);
	}


	public function cannonicalizePath(string $path): string
	{
		$path = strtr($path, DIRECTORY_SEPARATOR, '/');
		$parts = [];

		foreach (explode('/', $path) as $i => $name) {
			if ($name === '.' || ($name === '' && $i > 0)) continue;
			if ($name === '..') {
				array_pop($parts);
				continue;
			}

			$parts[] = $name;
		}

		return implode('/', $parts);
	}
}
