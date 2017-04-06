<?php

namespace Yarak\tests\functional;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SignatureParserTest extends \Codeception\Test\Unit
{
    /**
     * Path to temp classes file.
     *
     * @var string
     */
    protected $tempPath = __DIR__.'/../temp/';

    /**
     * Symfony console application.
     *
     * @var Application
     */
    protected $application;

    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();

        $this->tester->makeDirectoryStructure([
            $this->tester->getConfig()->getConsoleDirectory(),
            $this->tester->getConfig()->getCommandsDirectory(),
        ]);

        $this->application = new Application();

        array_map('unlink', glob($this->tempPath.'*'));
    }

    /**
     * @test
     */
    public function signature_parser_basic_arguments_work()
    {
        $output = $this->createCommand('ArgBasic', 'arg:basic {arg}')
             ->runCommand([
                'command' => 'arg:basic',
                'arg' => 'value',
            ])->fetch();

        $this->assertEquals('value', $this->getArguments($output, 'arg'));
    }

    /**
     * @test
     */
    public function signature_parser_optional_arguments_work()
    {
        $output = $this->createCommand('ArgOptional', 'arg:optional {arg?}')
            ->runCommand([
                'command' => 'arg:basic',
            ])->fetch();

        $this->assertNull($this->getArguments($output, 'arg'));
    }

    /**
     * @test
     */
    public function signature_parser_default_arguments_work()
    {
        $output = $this->createCommand('ArgDefault', 'arg:default {arg=default}')
            ->runCommand([
                'command' => 'arg:default',
            ])->fetch();

        $this->assertEquals('default', $this->getArguments($output, 'arg'));
    }

    /**
     * @test
     */
    public function signature_parser_argument_arrays_work()
    {
        $output = $this->createCommand('ArgArray', 'arg:array {arg*}')
            ->runCommand([
                'command' => 'arg:array',
                'arg' => ['one', 'two', 'three'],
            ])->fetch();

        $this->assertEquals(
            ['one', 'two', 'three'],
            $this->getArguments($output, 'arg')
        );
    }

    /**
     * @test
     */
    public function signature_parser_optional_argument_arrays_work()
    {
        $output = $this->createCommand('ArgOptArray', 'arg:opt-array {arg?*}')
            ->runCommand([
                'command' => 'arg:opt-array',
            ])->fetch();

        $this->assertEquals([], $this->getArguments($output, 'arg'));
    }

    /**
     * @test
     */
    public function signature_parser_basic_options_work()
    {
        $output = $this->createCommand('OptBasic', 'opt:basic {--opt}')
             ->runCommand([
                'command' => 'opt:basic',
                '--opt' => true,
            ])->fetch();

        $this->assertEquals(true, $this->getOptions($output, 'opt'));
    }

    /**
     * @test
     */
    public function signature_parser_required_value_options_work()
    {
        $output = $this->createCommand('OptRequired', 'opt:required {--opt=}')
             ->runCommand([
                'command' => 'opt:required',
                '--opt' => 'value',
            ])->fetch();

        $this->assertEquals('value', $this->getOptions($output, 'opt'));
    }

    /**
     * @test
     */
    public function signature_parser_default_value_options_work()
    {
        $output = $this->createCommand('OptDefault', 'opt:default {--opt=default}')
             ->runCommand([
                'command' => 'opt:default',
            ])->fetch();

        $this->assertEquals('default', $this->getOptions($output, 'opt'));
    }

    /**
     * @test
     */
    public function signature_parser_options_shortcut_work()
    {
        $output = $this->createCommand('OptShortcut', 'opt:shortcut {--o|opt}')
             ->runCommand([
                'command' => 'opt:shortcut',
                '-o' => true,
            ])->fetch();

        $this->assertEquals(true, $this->getOptions($output, 'opt'));
    }

    /**
     * @test
     */
    public function signature_parser_option_arrays_work()
    {
        $output = $this->createCommand('OptArray', 'opt:array {--opt=*}')
             ->runCommand([
                'command' => 'opt:array',
                '--opt' => ['one', 'two', 'three'],
            ])->fetch();

        $this->assertEquals(
            ['one', 'two', 'three'],
            $this->getOptions($output, 'opt')
        );
    }

    /**
     * Create a command with given class name and signature.
     *
     * @param string $className
     * @param string $signature
     *
     * @return $this
     */
    protected function createCommand($className, $signature)
    {
        $commandStub = file_get_contents(__DIR__.'/../_data/Stubs/command.stub');

        $commandStub = str_replace('CLASSNAME', $className, $commandStub);

        $commandStub = str_replace('SIGNATURE', $signature, $commandStub);

        file_put_contents(
            $path = $this->tempPath."{$className}.php",
            $commandStub
        );

        require_once $path;

        $namespace = "\\Yarak\\tests\\temp\\{$className}";

        $this->application->add(new $namespace());

        return $this;
    }

    /**
     * Run command using the given arguments.
     *
     * @param array $arguments
     *
     * @return BufferedOutput
     */
    protected function runCommand(array $arguments = [])
    {
        $input = new ArrayInput($arguments);

        $output = new BufferedOutput();

        $this->application->setAutoExit(false);

        $this->application->run($input, $output);

        return $output;
    }

    /**
     * Get an array of all arguments.
     *
     * @param mixed $key
     *
     * @return array
     */
    protected function getArguments($output, $key = null)
    {
        $output = json_decode($output, true);

        if (!is_null($key)) {
            return $output['arguments'][$key];
        }

        return $output['arguments'];
    }

    /**
     * Get an array of all options.
     *
     * @param mixed $key
     *
     * @return array
     */
    protected function getOptions($output, $key = null)
    {
        $output = json_decode($output, true);

        if (!is_null($key)) {
            return $output['options'][$key];
        }

        return $output['options'];
    }
}
