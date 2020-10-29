<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Commands;

use JuniWalk\Tessa\BundleManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class TessaWarmUpCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultName = 'tessa:warm-up';

	/** @var BundleManager */
	private $bundleManager;


	/**
	 * @param BundleManager  $bundleManager
	 */
	public function __construct(
		BundleManager $bundleManager
	) {
		$this->bundleManager = $bundleManager;
		parent::__construct();
	}


	protected function configure()
	{
		$this->setConfirm('This command will compile all available bundles, continue? <comment>[Y,n]</comment> ');
		$this->setDescription('Compile all available bundles.');
		$this->setName($this::$defaultName);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$bundleManager = $this->bundleManager;
		$bundles = $bundleManager->getBundles();

		foreach ($bundles as $bundle) {
			$bundleManager->compile($bundle, 'css');
			$bundleManager->compile($bundle, 'js');
		}

		return 0;
	}
}
