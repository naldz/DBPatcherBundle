<?php

namespace Naldz\Bundle\DBPatcherBundle\TestHelper\CommandExecutor;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandExecutor
{
    private $application;
    private $input;
    private $output;

    public function __construct($application)
    {
        $this->application = $application;
        $this->application->setCatchExceptions(false);
        $this->application->setAutoExit(false);
    }

    public function execute(array $input, array $options = array())
    {
        $this->input = new ArrayInput($input);
        if (isset($options['interactive'])) {
            $this->input->setInteractive($options['interactive']);
        }

        $this->output = new StreamOutput(fopen('php://memory', 'w', false));
        if (isset($options['decorated'])) {
            $this->output->setDecorated($options['decorated']);
        }
        if (isset($options['verbosity'])) {
            $this->output->setVerbosity($options['verbosity']);
        }

        $this->application->run($this->input, $this->output);

        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        return $display;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

}