<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends Command
{
	/** @var InputInterface */
	private $input;

	/** @var OutputInterface */
	private $output;

	/** @var string|null */
	private $confirm;


	/**
	 * @param  string  $message
	 * @return void
	 */
	public function setConfirm(string $message): void
	{
		$this->confirm = $message;
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		if (!$this->confirm) {
			return;
		}

		$question = new ConfirmationQuestion($this->confirm);

		if ($this->ask($question)) {
			$output->writeln('');
			return;
		}

		$this->terminate();
	}


	/**
	 * @return void
	 */
	protected function terminate(): void
	{
		$this->setCode(function(): int {
			return 0;
		});
	}


	/**
	 * @param  Question  $question
	 * @return mixed
	 */
	protected function ask(Question $question)
	{
		return $this->getHelper('question')->ask(
			$this->input,
			$this->output,
			$question
		);
	}
}
