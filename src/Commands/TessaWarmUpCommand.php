<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Commands;

use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\BundleManager;
use JuniWalk\Utils\Console\AbstractCommand;
use JuniWalk\Utils\Console\Tools\ProgressIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TessaWarmUpCommand extends AbstractCommand
{
	protected static $defaultDescription = 'Compile all available bundles.';
	protected static $defaultName = 'tessa:warm-up';


	public function __construct(
		private readonly BundleManager $bundleManager
	) {
		parent::__construct();
	}


	protected function configure()
	{
		$this->setDescription($this::$defaultDescription);
		$this->setName($this::$defaultName);
	}


	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->addQuestion(function($cli) {
			return $cli->confirm('This command will compile all available bundles, continue?');
		});

		parent::interact($input, $output);
	}


	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$status = new ProgressIndicator($output);
		$status->iterate($this->bundleManager->getBundles(), function($status, Bundle $bundle) {
			$status->setMessage($bundle->getName());

			$this->bundleManager->compile($bundle, 'css');
			$this->bundleManager->compile($bundle, 'js');
		});

		return Command::SUCCESS;
	}
}
