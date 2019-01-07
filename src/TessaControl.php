<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Assets\Asset;
use Nette\Application\UI\Control;
use Nette\Http\IRequest;
use Nette\Utils\Html;

final class TessaControl extends Control
{
	/** @var BundleManager */
	private $manager;

    /** @var string */
    private $basePath;

	/** @var string */
	private $wwwDir;


	/**
	 * @param  string  $wwwDir
	 * @param  IRequest  $httpRequest
	 * @param  BundleManager  $manager
	 */
	public function __construct(string $wwwDir, IRequest $httpRequest, BundleManager $manager)
	{
        $this->basePath = $httpRequest->getUrl()->getBasePath();
		$this->wwwDir = $wwwDir.'/';
		$this->manager = $manager;
	}


	/**
	 * @param  string  $bundle
	 * @return void
	 */
	public function renderCss(string $bundle = 'css'): void
	{
        $bundle = $this->manager->compile($bundle, 'css');
        $output = '';

        foreach ($bundle->getAssets() as $asset) {
            $html = Html::el('link rel="stylesheet"')->setHref($this->createPublicPath($asset));
            $output .= $html.PHP_EOL;
        }

        echo trim($output);
	}


	/**
	 * @param  string  $bundle
	 * @return void
	 */
	public function renderJs(string $bundle = 'default'): void
	{
        $bundle = $this->manager->compile($bundle, 'js');
        $output = '';

        foreach ($bundle->getAssets() as $asset) {
            $html = Html::el('script type="javascript"')->setSrc($this->createPublicPath($asset));
            $output .= $html.PHP_EOL;
        }

        echo trim($output);
	}


	/**
	 * @param  Asset  $asset
	 * @return string
	 */
	private function createPublicPath(Asset $asset): string
	{
		return str_replace($this->wwwDir, $this->basePath, $asset->getFile());
	}
}
