<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\Tessa\BundleManager;
use Nette\DI\Container;
use Tester\Assert;
use Tester\Helpers as FileSystem;
use Tester\TestCase;

require __DIR__.'/../bootstrap.php';

/**
 * @testCase
 */
final class CompileBundleTest extends TestCase
{
	private BundleManager $bundleManager;
	private Container $container;

	public function __construct() {
		$this->container = createContainer();
	}

	public function setUp() {
		$this->bundleManager = $this->container->getByType(BundleManager::class);
	}

	public function tearDown() {
		FileSystem::purge(OutputStorage);
		unset($this->bundleManager);
	}


	public function testBundleCombinedCompilation(): void
	{
		$bundle = $this->bundleManager->compile('default', 'js');
		$patterns = [
			'defaultjs-script.js' => '#/static/defaultjs-script.js$#i',
			'module.mjs' => '#/assets/module.mjs$#i',
		];

		Assert::same(null, $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();
			$name = $asset->getName();

			Assert::hasKey($name, $patterns);
			Assert::match($patterns[$name], $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleModuleCompilation(): void
	{
		$bundle = $this->bundleManager->compile('module', 'js');
		$patterns = [
			'script.js' => '#/assets/script.js$#i',
			'module.mjs' => '#/assets/module.mjs$#i',
		];

		Assert::same('module', $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();
			$name = $asset->getName();

			Assert::hasKey($name, $patterns);
			Assert::match($patterns[$name], $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleExtendedCompilation(): void
	{
		$bundle = $this->bundleManager->compile('extended', 'js');
		$patterns = [
			'defaultjs-script.js'	=> '#/static/defaultjs-script.js$#i',
			'module.mjs'			=> '#/assets/module.mjs$#i',
			'fullcalendar.mjs'		=> '#/assets/fullcalendar.mjs$#i',
			'extendedjs-form.js'	=> '#/static/extendedjs-form.js$#i',
		];

		Assert::same(null, $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();
			$name = $asset->getName();

			Assert::hasKey($name, $patterns);
			Assert::match($patterns[$name], $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleDirectLinkingCompilation(): void
	{
		$this->bundleManager->setStorage(null);

		$bundle = $this->bundleManager->compile('default', 'js');
		$patterns = [
			'script.js' => '#/assets/script.js$#i',
			'module.mjs' => '#/assets/module.mjs$#i',
		];

		Assert::same(null, $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();
			$name = $asset->getName();

			Assert::hasKey($name, $patterns);
			Assert::match($patterns[$name], $file);
			Assert::true(is_file($file));
		}
	}
}

(new CompileBundleTest)->run();
