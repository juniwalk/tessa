<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Assets\Asset;
use Nette\Http\IRequest;
use Nette\Utils\Strings;

final class UrlFixerFilter implements Filter
{
    /** @var string */
    const URL_PATTERN = '/url\(\s*([\'"])?(?<url>[^\)]+)(?(1)\1)\s*\)/ix';

    /** @var string */
    private $docRoot;

    /** @var string */
    private $basePath;


    /**
     * @param string  $docRoot
     * @param IRequest  $http
     */
    public function __construct(string $docRoot = '/', IRequest $http)
    {
        $this->basePath = $http->getUrl()->getBasePath();
        $this->docRoot = $docRoot;
    }


    /**
     * @param  string  $content
     * @param  Asset  $asset
     * @return string
     */
    public function apply(string $content, Asset $asset): string
    {
        if (!$asset->isTypeOf('css')) {
            return $content;
        }

        $urls = Strings::matchAll($content, self::URL_PATTERN);

        foreach ($urls as $url) {
            $absoluteUrl = $this->absolutizeUrl($url['url'], $asset);
            $absoluteUrl = 'url(\''.$absoluteUrl.'\')';

            $content = str_replace($url[0], $absoluteUrl, $content);
        }

        return $content;
    }


	/**
	 * @param  string  $url
	 * @param  Asset  $asset
	 * @return string
	 */
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


	/**
	 * @param  string  $path
	 * @return string
	 */
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
