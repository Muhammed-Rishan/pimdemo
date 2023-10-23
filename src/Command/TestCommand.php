<?php

// src/Command/AwesomeCommand.php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('test:command')
            ->setDescription('An awesome custom command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('<info>This is an awesome custom command.</info>');

        $output->writeln('<comment>This is an awesome custom command.</comment>');

        $output->writeln('<error>This is an awesome custom command.</error>');

        $output->writeln('<question>This is an awesome custom command.</question>');

        $this->dumpVerbose("Dump verbose");

        $this->dump("Isn't that awesome?");

        return Command::SUCCESS;
    }
}
