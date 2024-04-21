<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use Nette\ComponentModel\IComponent as Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest as HttpRequest;
use Nette\Utils\Html;
use ReflectionAttribute;
use ReflectionClass;

final class TessaControl extends Control
{
	private readonly string $basePath;
	private readonly string $wwwDir;

	/** @var array<string, bool> */
	private array $history = [];

	public function __construct(
		string $wwwDir,
		HttpRequest $httpRequest,
		private readonly BundleManager $bundleManager,
	) {
		$this->basePath = $httpRequest->getUrl()->getBasePath();
		$this->wwwDir = rtrim($wwwDir, '/').'/';
	}


	public function renderCss(string $bundle = 'default'): void
	{
		$bundle = $this->bundleManager->compile($bundle, 'css');
		$output = '';

		foreach ($bundle->getAssets() as $asset) {
			$file = $this->createPath($asset);

			if ($this->history[$file] ?? false) {
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
		$bundleType = $bundle->getAttribute('type');
		$output = '';

		foreach ($bundle->getAssets() as $asset) {
			$file = $this->createPath($asset);

			if ($this->history[$file] ?? false) {
				continue;
			}

			$html = Html::el('script type="text/javascript"')
				->addAttributes($bundle->getAttributes())
				->setSrc($file);

			if ($bundle->getAttribute('cookie-consent')) {
				$html->setAttribute('type', 'text/plain');
			}

			if ($asset->isModule() || $bundleType == 'module') {
				$html->setAttribute('type', 'module');
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


	private function createPath(Asset $asset): string
	{
		return str_replace($this->wwwDir, $this->basePath, $asset->getPath());
	}
}
