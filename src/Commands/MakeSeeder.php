<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\DB\Seeders\SeederCreator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSeeder extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('make:seeder')
            ->setDescription('Create a new seeder file.')
            ->setHelp('This command will generate a new seeder file.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of your seeder file.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $config = Config::getInstance($this->configArray);

        $creator = new SeederCreator($config);

        $creator->create($name);

        $output->writeln("<info>Successfully created seeder {$name}.</info>");
    }
}
