<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class ConsoleGenerateTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();
    }

    /**
     * @test
     */
    public function console_generate_command_makes_all_directories_and_files()
    {
        $paths = $this->getPaths();

        $this->tester->assertAllPathsDontExist($paths);

        Yarak::call('console:generate', [], DI::getDefault());

        $this->tester->assertAllPathsExist($paths);

        $this->tester->removeConsoleDirectory();
    }

    /**
     * Return all paths necessary.
     *
     * @return array
     */
    protected function getPaths()
    {
        $config = $this->tester->getConfig();

        return [
            $config->getConsoleDirectory(),
            $config->getCommandsDirectory(),
            $config->getConsoleDirectory('Kernel.php'),
        ];
    }
}
