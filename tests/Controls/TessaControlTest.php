<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\Tessa\Attributes\AssetBundle;
use JuniWalk\Tessa\Exceptions\AssetTypeException;
use JuniWalk\Tessa\TessaControl;
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
	public function __construct(
		private readonly TessaControl $tessaControl,
	) {
		$this->changeAction('default');
	}

	#[AssetBundle('calendar')]
	public function actionDefault(): void {}

	protected function createComponentTessa(): TessaControl {
		return $this->tessaControl;
	}
}

/**
 * @testCase
 */
final class TessaControlTest extends TestCase
{
	private TessaControl $tessa;
	private Container $container;

	public function __construct() {
		$this->container = createContainer();
	}

	public function setUp() {
		$presenter = $this->container->createInstance(TessaPresenter::class);

		$this->tessa = $presenter->getComponent('tessa');
		$this->tessa->clearHistory();
	}

	public function tearDown() {
		FileSystem::purge(OutputStorage);

		$presenter = $this->tessa->getParent();
		$presenter?->removeComponent($this->tessa);

		unset($this->tessa);
	}


	public function testRenderClassicAssets(): void
	{
		$output = Output::capture(fn() => $this->tessa->renderJs('default'));

		Assert::contains('/static/defaultjs-script.js', $output);
		Assert::contains('/assets/module.mjs', $output);
	}


	public function testRenderAutoAssets(): void
	{
		$output = Output::capture(fn() => $this->tessa->render('js'));

		Assert::contains('/static/defaultjs-script.js', $output);
		Assert::contains('/assets/fullcalendar.mjs', $output);
		Assert::contains('/assets/module.mjs', $output);
	}


	public function testRenderInvalidType(): void
	{
		Assert::exception(
			fn() => $this->tessa->render('unknown'),
			AssetTypeException::class,
			'Unknown type of "%w%"',
		);
	}
}

(new TessaControlTest)->run();
