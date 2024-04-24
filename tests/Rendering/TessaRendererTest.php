<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use JuniWalk\Tessa\TessaRenderer;
use JuniWalk\Tessa\Traits\AssetManager;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Tester\Assert;
use Tester\Helpers as FileSystem;
use Tester\TestCase;
use Tracy\Helpers as Output;

require __DIR__.'/../bootstrap.php';

#[AssetBundle('default')]
class TessaPresenter extends Presenter
{
	use AssetManager;

	#[AssetBundle('calendar')]
	public function actionDefault(): void {}

	#[AssetBundle('calendar', true)]
	public function actionPartial(): void {}
}

/**
 * @testCase
 */
final class TessaRendererTest extends TestCase
{
	private TessaRenderer $tessa;
	private Container $container;

	public function __construct() {
		$this->container = createContainer();
	}

	public function setUp() {
		$presenter = $this->container->createInstance(TessaPresenter::class);
		$this->container->callInjects($presenter);

		$this->tessa = $presenter->getComponent('tessa');
		$this->tessa->clearHistory();
	}

	public function tearDown() {
		$presenter = $this->tessa->getParent();
		$presenter?->removeComponent($this->tessa);

		FileSystem::purge(OutputStorage);
		unset($this->tessa);
	}


	public function testRenderClassicAssets(): void
	{
		$output = Output::capture(fn() => $this->tessa->renderJs('default'));

		Assert::contains('/static/defaultjs-script.js', $output);
		Assert::contains('/assets/module.mjs', $output);

		Assert::notContains('defer', $output);
	}


	public function testRenderAutoAssets(): void
	{
		$this->tessa->getPresenter()->changeAction('default');
		$output = Output::capture(fn() => $this->tessa->renderJs());

		Assert::contains('/static/defaultjs-script.js', $output);
		Assert::contains('/assets/module.mjs', $output);
		Assert::contains('/assets/fullcalendar.mjs', $output);
	}


	public function testRenderClearPreviousAssets(): void
	{
		$this->tessa->getPresenter()->changeAction('partial');
		$output = Output::capture(fn() => $this->tessa->renderJs());

		Assert::notContains('/static/defaultjs-script.js', $output);
		Assert::notContains('/assets/module.mjs', $output);
		Assert::contains('/assets/fullcalendar.mjs', $output);

		Assert::contains('defer', $output);
	}


	public function testRenderInvalidType(): void
	{
		Assert::exception(
			fn() => $this->tessa->render(Type::Unknown),
			AssetTypeException::class,
			'Type of "%a%" is not supported %A?%',
		);

		Assert::exception(
			fn() => $this->tessa->render('unknown'),
			AssetTypeException::class,
			'Type of "%a%" is not supported %A?%',
		);
	}
}

(new TessaRendererTest)->run();
