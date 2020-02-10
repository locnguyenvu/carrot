<?php
namespace Carrot\Console;

class Parser
{
    private $name;
    private $argvInput;
    private $arguments = [];

    public function __construct(string $commandPattern)
    {
        if (empty($commandPattern)) {
            throw new \Carrot\Exception\InvalidCommandException();
        }
        $elements = explode(' ', $commandPattern);

        $this->name = array_shift($elements);
        foreach ($elements as $argument) {
            if (!preg_match('/\{.*\}/', $argument) || !preg_match('/\{\-{2}.*\}/', $argument)) continue;
            else {
                $this->arguments[trim(str_replace(['{', '}'], '', $argument))] = null;
            }
        }
    }

    public function setArgvInput(Input\ArgvInput $input) : void
    {
        $this->argvInput = $input;
    }

    public function getOption($key) : ?string
    {
        return $this->argvInput->getOption($key);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getArguments() : array
    {
        $inputArgs = $this->argvInput->getArguments();
        array_shift($inputArgs); // Remove command name
        $args = [];
        foreach($inputArgs as $arg) {
            if ($arg == $this->name) continue;
            $args[] = $arg;
        }

        return $args;
    }
}