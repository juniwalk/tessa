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
	private BundleManager $manager;
	private array $history = [];

	public function __construct(BundleManager $manager)
	{
		$this->manager = $manager;
	}


	public function renderCss(string $bundle = 'default'): void
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

			if ($cookieConsent = $bundle->getCookieConsent()) {
				$html->setAttribute('cookie-consent', $cookieConsent);
				$html->setAttribute('type', 'text/plain');
			}

			if ($bundle->isDeferred()) {
				$html->setAttribute('defer', 'defer');
			}

			if ($bundle->isAsync()) {
				$html->setAttribute('async', 'async');
			}

			$this->history[$file] = true;
			$output .= $html.PHP_EOL;
		}

		echo trim($output);
	}
}
