<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Commands;

use JuniWalk\Tessa\BundleManager;
use JuniWalk\Tessa\Enums\Type;
use JuniWalk\Utils\Console\AbstractCommand;
use JuniWalk\Utils\Console\Tools\ProgressIndicator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'tessa:warm-up', description: 'Compile all available bundles.')]
final class TessaWarmUpCommand extends AbstractCommand
{
	public function __construct(
		private readonly BundleManager $bundleManager,
	) {
		parent::__construct();
	}


	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->addQuestion(fn($cli) => $cli->confirm('This command will compile all available bundles, continue?'));
		parent::interact($input, $output);
	}


	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$status = new ProgressIndicator($output);
		$status->iterate($this->bundleManager->getBundles(), function($status, string $bundleName) {
			$status->setMessage($bundleName);

			$this->bundleManager->compile($bundleName, Type::StyleSheet);
			$this->bundleManager->compile($bundleName, Type::JavaScript);
		});

		return Command::SUCCESS;
	}
}
