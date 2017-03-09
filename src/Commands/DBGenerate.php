<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\DB\DirectoryCreator;
use Yarak\Migrations\MigrationCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBGenerate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('db:generate')
            ->setDescription('Generate the database directory structure.')
            ->setHelp(
                'This command will create all the database directories and files necessary for yarak to run.'
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
        $config = Config::getInstance($this->configArray);

        $creator = new DirectoryCreator($config);

        $creator->create();

        foreach ($migrator->getLog() as $message) {
            $output->writeln($message);
        }
    }
}
