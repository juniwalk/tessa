<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use Nette\Application\UI\Control;
use Nette\Utils\Html;

final class TessaControl extends Control
{
	/** @var BundleManager */
	private $manager;

	/** @var string[] */
	private $history = [];


	/**
	 * @param  BundleManager  $manager
	 */
	public function __construct(BundleManager $manager)
	{
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
			$file = $bundle->createPublicPath($asset);

			if (isset($this->history[$file])) {
				continue;
			}

			$html = Html::el('link rel="stylesheet"')
				->setHref($file);

			$this->history[$file] = true;
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
			$file = $bundle->createPublicPath($asset);

			if (isset($this->history[$file])) {
				continue;
			}

			$html = Html::el('script type="text/javascript"')
				->setSrc($file);

			$this->history[$file] = true;
            $output .= $html.PHP_EOL;
        }

        echo trim($output);
	}
}
