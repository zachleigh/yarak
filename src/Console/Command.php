<?php

namespace Yarak\Console;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Yarak\Console\Input\Input;
use Yarak\Console\Output\SymfonyOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    /**
     * Symfony console output.
     *
     * @var SymfonyOutput
     */
    protected $output;

    /**
     * Symfony input implementation.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Command signature.
     *
     * @var null|string
     */
    protected $signature = null;

    /**
     * Configure the command if signature is set.
     */
    protected function configure()
    {
        if (!is_null($this->signature)) {
            $parser = new SignatureParser($this);

            $parser->parse($this->signature);

            $this->setDescription($this->description);
        }
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyOutput($output);

        $this->input = $input;

        if (method_exists($this, 'handle')) {
            $this->handle();
        }
    }

    /**
     * Return the output implementation.
     *
     * @return SymfonyOutput
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Get root symfony output implementation.
     *
     * @return OutputInterface
     */
    protected function getOutputInterface()
    {
        return $this->getOutput()->getOutput();
    }

    /**
     * Get the value of a command argument.
     *
     * @param string $key
     *
     * @return string|array
     */
    protected function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Determine if the given argument is present.
     *
     * @param string|int $name
     *
     * @return bool
     */
    protected function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command option.
     *
     * @param string $key
     *
     * @return string|array
     */
    protected function option($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Determine if the given option is present.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Add input to the command.
     *
     * @param Input $input
     */
    public function addInput(Input $input)
    {
        $reflection = new \ReflectionClass($input);

        $method = 'add'.$reflection->getShortName();

        if (method_exists($this, $method)) {
            $this->$method(...array_values($input->getAttributes()));
        }
    }

    /**
     * Ask for a confirmation.
     *
     * @param $text
     * @return mixed
     */
    public function confirm($text)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($text, false);

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }

    /**
     * Ask a question.
     *
     * @param $question
     * @param mixed|null $default
     * @return mixed
     */
    public function ask($question, $default = null)
    {
        $helper = $this->getHelper('question');
        $question = new Question($question, $default);

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }

    /**
     * Ask a password
     *
     * @param $question
     * @return mixed
     */
    public function askPassword($question)
    {
        $helper = $this->getHelper('question');

        $question = new Question($question);
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }

    /**
     * Ask a question where the answer is available from a list of predefined choices.
     *
     * @param $question
     * @param array $choices
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function choose($question, array $choices, $default = null)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setErrorMessage('%s is not a valid answer.');

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }

    /**
     * Ask a question where some auto-completion help is provided.
     *
     * @param $question
     * @param array $autoCompletion
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function anticipate($question, array $autoCompletion, $default = null)
    {
        $helper = $this->getHelper('question');
        $question = new Question($question, $default);
        $question->setAutocompleterValues($autoCompletion);

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }

    /**
     * Ask a question where the answer is available from a list of predefined choices and more choices can be selected.
     *
     * @param $question
     * @param array $choices
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function choice($question, array $choices, $default = null)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMultiselect(true);

        return $helper->ask($this->input, $this->getOutputInterface(), $question);
    }
}
