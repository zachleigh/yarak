<?php

namespace Yarak\Commands;

use Yarak\Output\SymfonyOutput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollback extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate:rollback')
            ->setDescription('Rollback migrations by given number of steps.')
            ->setHelp('This command allows you to rollback migrations.')
            ->addOption(
                'steps',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of steps to rollback.',
                1
            );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getMigrator(new SymfonyOutput($output))
            ->rollback($input->getOption('steps'));
    }
}
