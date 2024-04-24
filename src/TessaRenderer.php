<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use Nette\ComponentModel\IComponent as Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest as HttpRequest;
use Nette\Utils\Html;
use ReflectionClass;

final class TessaRenderer extends Control
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


	public function clearHistory(): void
	{
		$this->history = [];
	}


	public function renderCss(?string $bundle = null): void
	{
		$type = Type::StyleSheet;

		if (!$bundle) {
			$this->render($type);
			return;
		}

		echo implode(PHP_EOL, $this->compile($bundle, $type)).PHP_EOL;
	}


	public function renderJs(?string $bundle = null): void
	{
		$type = Type::JavaScript;

		if (!$bundle) {
			$this->render($type);
			return;
		}

		echo implode(PHP_EOL, $this->compile($bundle, $type)).PHP_EOL;
	}


	/**
	 * @throws AssetTypeException
	 */
	public function render(Type|string $type): void
	{
		$type = Type::make($type, false) ?? $type;

		if (!$type instanceof Type) {
			throw AssetTypeException::fromType($type);
		}

		$control = $this->getPresenter();
		$attributes = $this->findAssetAttributes($control);

		foreach ($control->getComponents() as $component) {
			$attributes += $this->findAssetAttributes($component);
		}

		$output = [];

		foreach ($attributes as $attribute) {
			if ($attribute->clearPrevious) {
				$output = [];
			}

			$bundle = $this->compile($attribute->bundleName, $type);
			$output = array_merge($output, $bundle);
		}

		echo implode(PHP_EOL, $output).PHP_EOL;
	}


	/**
	 * @return Html[]
	 * @throws AssetTypeException
	 */
	private function compile(string $bundle, Type $type): array
	{
		$bundle = $this->bundleManager->compile($bundle, $type);
		$create = match ($type) {
			Type::StyleSheet => $this->createStyleSheet(...),
			Type::JavaScript => $this->createJavaScript(...),

			default => throw AssetTypeException::fromType($type),
		};

		$output = [];

		foreach ($bundle->getAssets() as $asset) {
			$output[] = $create($asset, $bundle);
		}

		return array_filter($output);
	}


	private function createStyleSheet(Asset $asset, Bundle $bundle): ?Html
	{
		$path = $this->createFilePath($asset);

		if ($this->history[$path] ?? false) {
			return null;
		}

		$html = Html::el('link rel="stylesheet"')
			->setHref($path);

		$this->history[$path] = true;
		return $html;
	}


	private function createJavaScript(Asset $asset, Bundle $bundle): ?Html
	{
		$path = $this->createFilePath($asset);
		$type = $bundle->getAttribute('type');

		if ($this->history[$path] ?? false) {
			return null;
		}

		$html = Html::el('script type="text/javascript"')
			->addAttributes($bundle->getAttributes())
			->setSrc($path);

		if ($bundle->getAttribute('cookie-consent')) {
			$html->setAttribute('type', 'text/plain');
		}

		if ($asset->isModule() || $type === 'module') {
			$html->setAttribute('type', 'module');
		}

		$this->history[$path] = true;
		return $html;
	}


	private function createFilePath(Asset $asset): string
	{
		return str_replace($this->wwwDir, $this->basePath, $asset->getPath());
	}


	/**
	 * @return AssetBundle[]
	 */
	private function findAssetAttributes(Component $control): array
	{
		$class = new ReflectionClass($control);
		$attributes = $class->getAttributes(AssetBundle::class);

		if ($parent = $class->getParentClass()) {
			$items = $parent->getAttributes(AssetBundle::class);
			$attributes = array_merge($attributes, $items);
		}

		if ($control instanceof Presenter && $view = $control->getAction()) {
			$viewAction = Presenter::formatActionMethod($view);
			$viewRender = Presenter::formatRenderMethod($view);

			if ($class->hasMethod($viewAction)) {
				$items = $class->getMethod($viewAction)->getAttributes(AssetBundle::class);
				$attributes = array_merge($attributes, $items);
			}

			if ($class->hasMethod($viewRender)) {
				$items = $class->getMethod($viewRender)->getAttributes(AssetBundle::class);
				$attributes = array_merge($attributes, $items);
			}
		}

		return array_map(fn($x) => $x->newInstance(), $attributes);
	}
}
