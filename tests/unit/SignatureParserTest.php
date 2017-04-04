<?php

namespace Yarak\tests\unit;

use Yarak\Console\SignatureParser;
use Yarak\Console\Stubs\NullCommand;

class SignatureParserTest extends \Codeception\Test\Unit
{
    public function setUp()
    {
        parent::setUp();

        $this->command = new NullCommand();

        $this->parser = new SignatureParser($this->command);
    }
    /**
     * @test
     */
    public function signature_parser_parses_signature_name()
    {
        $signature = 'example:command {arg} {--opt}';

        $this->parser->parse($signature);

        $this->assertEquals('example:command', $this->command->getName());
    }

    /**
     * @test
     */
    public function signature_parser_parses_standard_argument()
    {
        $signature = 'example:command {arg} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(1, $args[0]['mode']);

        $this->assertNull($args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_argument_description()
    {
        $signature = 'example:command {arg : description} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(1, $args[0]['mode']);

        $this->assertEquals('description', $args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_optional_argument()
    {
        $signature = 'example:command {arg?} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(2, $args[0]['mode']);

        $this->assertNull($args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_optional_argument_with_description()
    {
        $signature = 'example:command {arg? : description} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(2, $args[0]['mode']);

        $this->assertEquals('description', $args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_default_argument_value()
    {
        $signature = 'example:command {arg=default} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(2, $args[0]['mode']);

        $this->assertNull($args[0]['description']);

        $this->assertEquals('default', $args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_default_argument_value_with_description()
    {
        $signature = 'example:command {arg=default : description} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(2, $args[0]['mode']);

        $this->assertEquals('description', $args[0]['description']);

        $this->assertEquals('default', $args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_argument_array_with_required_argument()
    {
        $signature = 'example:command {arg*} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(5, $args[0]['mode']);

        $this->assertNull($args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_argument_array_with_optional_argument()
    {
        $signature = 'example:command {arg?*} {--opt}';

        $this->parser->parse($signature);

        $args = $this->command->getArguments();

        $this->assertCount(1, $args);

        $this->assertEquals('arg', $args[0]['name']);

        $this->assertEquals(6, $args[0]['mode']);

        $this->assertNull($args[0]['description']);

        $this->assertNull($args[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_standard_option()
    {
        $signature = 'example:command {arg} {--opt}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(1, $options[0]['mode']);

        $this->assertNull($options[0]['description']);

        $this->assertNull($options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_description()
    {
        $signature = 'example:command {arg} {--opt : description}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(1, $options[0]['mode']);

        $this->assertEquals('description', $options[0]['description']);

        $this->assertNull($options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_with_required_value()
    {
        $signature = 'example:command {arg} {--opt=}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(2, $options[0]['mode']);

        $this->assertNull($options[0]['description']);

        $this->assertNull($options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_with_default_value()
    {
        $signature = 'example:command {arg} {--opt=default}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(4, $options[0]['mode']);

        $this->assertNull($options[0]['description']);

        $this->assertEquals('default', $options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_with_default_value_and_description()
    {
        $signature = 'example:command {arg} {--opt=default : description}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(4, $options[0]['mode']);

        $this->assertEquals('description', $options[0]['description']);

        $this->assertEquals('default', $options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_with_shortcut()
    {
        $signature = 'example:command {arg} {--o|opt}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertEquals('o', $options[0]['shortcut']);

        $this->assertEquals(1, $options[0]['mode']);

        $this->assertNull($options[0]['description']);

        $this->assertNull($options[0]['default']);
    }

    /**
     * @test
     */
    public function signature_parser_parses_option_array_with_required_value()
    {
        $signature = 'example:command {arg} {--opt=*}';

        $this->parser->parse($signature);

        $options = $this->command->getOptions();

        $this->assertCount(1, $options);

        $this->assertEquals('opt', $options[0]['name']);

        $this->assertNull($options[0]['shortcut']);

        $this->assertEquals(10, $options[0]['mode']);

        $this->assertNull($options[0]['description']);

        $this->assertNull($options[0]['default']);
    }
}
