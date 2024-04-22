<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\Tessa\BundleManager;
use Nette\DI\Container;
use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;

require __DIR__.'/../bootstrap.php';

/**
 * @testCase
 */
final class CompileBundleTest extends TestCase
{
	private BundleManager $bundleManager;
	private Container $container;

	public function __construct()
	{
		$this->container = createContainer();
	}

	public function setUp()
	{
		$this->bundleManager = $this->container->getByType(BundleManager::class);
	}

	public function tearDown()
	{
		Helpers::purge(OutputStorage);
		unset($this->bundleManager);
	}


	public function testBundleCombinedCompilation(): void
	{
		$bundle = $this->bundleManager->compile('default', 'js');
		Assert::same(null, $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			Assert::match('#/(assets|static)/#i', $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleModuleCompilation(): void
	{
		$bundle = $this->bundleManager->compile('module', 'js');
		Assert::same('module', $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			Assert::match('#/(assets)/#i', $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleDirectLinkingCompilation(): void
	{
		$this->bundleManager->setDirectLinking(true);

		$bundle = $this->bundleManager->compile('default', 'js');
		Assert::same(null, $bundle->getAttribute('type'));

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			Assert::match('#/(assets)/#i', $file);
			Assert::true(is_file($file));
		}
	}
}

(new CompileBundleTest)->run();
