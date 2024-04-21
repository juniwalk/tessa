<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\Tessa\BundleManager;
use JuniWalk\Tessa\Bundles\AssetBundle;
use JuniWalk\Tessa\Storage;
use Nette\Http\RequestFactory;
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

	public function setUp()
	{
		$storage = new Storage(OutputStorage, true);
		$request = (new RequestFactory)->fromGlobals();
		$bundleManager = new BundleManager(DocumentRoot, $storage, $request);

		$bundle = new AssetBundle('default');
		$bundle->discoverAsset(AssetsStorage.'/module.mjs');
		$bundle->discoverAsset(AssetsStorage.'/script.js');

		$bundleManager->addBundle($bundle);
		$this->bundleManager = $bundleManager;
	}


	public function testBundleCombinedCompilation(): void
	{
		$bundle = $this->bundleManager->compile('default', 'js');

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			Assert::match('#/(assets|static)/#i', $file);
			Assert::true(is_file($file));
		}
	}


	public function testBundleModuleCompilation(): void
	{
		$this->bundleManager->getBundle('default')->setModule(true);
		$bundle = $this->bundleManager->compile('default', 'js');

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

		foreach ($bundle->getAssets() as $asset) {
			$file = $asset->getFile();

			Assert::match('#/(assets)/#i', $file);
			Assert::true(is_file($file));
		}
	}


	public function tearDown()
	{
		Helpers::purge(OutputStorage);
		unset($this->bundleManager);
	}
}

(new CompileBundleTest)->run();
