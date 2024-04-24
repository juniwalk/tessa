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
		$this->renderType(Type::StyleSheet, $bundle);
	}


	public function renderJs(?string $bundle = null): void
	{
		$this->renderType(Type::JavaScript, $bundle);
	}


	/**
	 * @throws AssetTypeException
	 */
	public function renderType(Type|string $type, ?string $bundle = null): void
	{
		$type = Type::make($type, false) ?? $type;

		if (!$type instanceof Type) {
			throw AssetTypeException::fromType($type);
		}

		match (true) {
			$bundle => $this->print(
				$this->compile($bundle, $type)
			),
			default => $this->render($type),
		};
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
		$attrs = $this->findAttributes($control);

		foreach ($control->getComponents() as $component) {
			$attrs = array_merge($attrs, $this->findAttributes($component));
		}

		$output = [];

		foreach ($attrs as $attr) {
			if ($attr->clearPrevious) {
				$output = [];
			}

			$assets = $this->compile($attr->bundleName, $type);
			$output = array_merge($output, $assets);
		}

		$this->print($output);
	}


	/**
	 * @return Html[]
	 * @throws AssetTypeException
	 */
	private function compile(string $bundle, Type $type): array
	{
		$assets = $this->bundleManager->compile($bundle, $type);
		$create = match ($type) {
			Type::StyleSheet => $this->createStyleSheet(...),
			Type::JavaScript => $this->createJavaScript(...),

			default => throw AssetTypeException::fromType($type),
		};

		$output = [];

		foreach ($assets as $asset) {
			$output[] = $create($asset);
		}

		return array_filter($output);
	}


	/**
	 * @param Html[] $assets
	 */
	private function print(array $assets): void
	{
		echo implode(PHP_EOL, $assets).PHP_EOL;
	}


	private function createStyleSheet(Asset $asset): ?Html
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


	private function createJavaScript(Asset $asset): ?Html
	{
		$path = $this->createFilePath($asset);
		$type = $asset->getAttribute('type');

		if ($this->history[$path] ?? false) {
			return null;
		}

		$html = Html::el('script type="text/javascript"')
			->addAttributes($asset->getAttributes())
			->setSrc($path);

		if ($asset->getAttribute('cookie-consent')) {
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
	private function findAttributes(Component $control): array
	{
		$class = new ReflectionClass($control);
		$attrs = $class->getAttributes(AssetBundle::class);

		if ($parent = $class->getParentClass()) {
			$items = $parent->getAttributes(AssetBundle::class);
			$attrs = array_merge($attrs, $items);
		}

		if ($control instanceof Presenter && $view = $control->getAction()) {
			$viewAction = Presenter::formatActionMethod($view);
			$viewRender = Presenter::formatRenderMethod($view);

			if ($class->hasMethod($viewAction)) {
				$items = $class->getMethod($viewAction)->getAttributes(AssetBundle::class);
				$attrs = array_merge($attrs, $items);
			}

			if ($class->hasMethod($viewRender)) {
				$items = $class->getMethod($viewRender)->getAttributes(AssetBundle::class);
				$attrs = array_merge($attrs, $items);
			}
		}

		return array_map(fn($x) => $x->newInstance(), $attrs);
	}
}
