<?php

namespace Yarak\Commands;

use Yarak\Output\SymfonyOutput;
use Yarak\DB\Seeders\SeedRunner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBSeed extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('db:seed')
            ->setDescription('Seed the database.')
            ->setHelp('This command will run the given seeder class.')
            ->addArgument(
                'class',
                InputArgument::OPTIONAL,
                'The name of the seeder class to run.',
                'DatabaseSeeder'
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
        $symfonyOutput = new SymfonyOutput($output);

        $seedRunner = new SeedRunner($symfonyOutput);

        $seedRunner->run($input->getArgument('class'));

        foreach ($seedRunner->getLog() as $message) {
            $output->writeln($message);
        }
    }
}
