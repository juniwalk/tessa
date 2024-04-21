<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Bundles\ReadOnlyBundle;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use Nette\ComponentModel\IComponent as Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;
use ReflectionAttribute;
use ReflectionClass;

final class TessaControl extends Control
{
	/** @var array<string, bool> */
	private array $history = [];

	public function __construct(
		private readonly BundleManager $bundleManager,
	) {
	}


	public function renderCss(string $bundle = 'default'): void
	{
		$bundle = $this->bundleManager->compile($bundle, 'css');
		$output = '';

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			if ($bundle instanceof ReadOnlyBundle) {
				$file = $bundle->createPublicPath($asset);
			}

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
		$bundle = $this->bundleManager->compile($bundle, 'js');
		$output = '';

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			if ($bundle instanceof ReadOnlyBundle) {
				$file = $bundle->createPublicPath($asset);
			}

			if (isset($this->history[$file])) {
				continue;
			}

			$html = Html::el('script type="text/javascript"')
				->setSrc($file);

			if ($cookieConsent = $bundle->getCookieConsent()) {
				$html->setAttribute('cookie-consent', $cookieConsent);
				$html->setAttribute('type', 'text/plain');
			}

			if ($asset->isModule() || $bundle->isModule()) {
				$html->setAttribute('type', 'module');
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


	/**
	 * @throws AssetTypeException
	 */
	public function render(string $type): void
	{
		$control = $this->getPresenter();
		$renderMethod = match ($type) {
			'css' => $this->renderCss(...),
			'js' => $this->renderJs(...),

			default => throw AssetTypeException::fromType($type),
		};

		$bundles = $this->findAssetBundles($control);

		foreach ($control->getComponents() as $component) {
			$bundles += $this->findAssetBundles($component);
		}

		foreach ($bundles as $bundle) {
			$renderMethod($bundle->newInstance()->bundleName);
		}
	}


	/**
	 * @return ReflectionAttribute<AssetBundle>[]
	 */
	private function findAssetBundles(Component $control): array
	{
		$class = new ReflectionClass($control);
		$bundles = $class->getAttributes(AssetBundle::class);

		if ($parent = $class->getParentClass()) {
			$bundles = array_merge($bundles, $parent->getAttributes(AssetBundle::class));
		}

		if ($control instanceof Presenter && $view = $control->getAction()) {
			$viewAction = Presenter::formatActionMethod($view);
			$viewRender = Presenter::formatRenderMethod($view);

			if ($class->hasMethod($viewAction)) {
				$bundles = array_merge($bundles, $class->getMethod($viewAction)->getAttributes(AssetBundle::class));
			}

			if ($class->hasMethod($viewRender)) {
				$bundles = array_merge($bundles, $class->getMethod($viewRender)->getAttributes(AssetBundle::class));
			}
		}

		return $bundles;
	}
}
