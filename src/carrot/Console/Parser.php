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
            if (!preg_match('/\{.*\}/', $argument)) continue;

            $argumentName = trim(str_replace(['{', '}'], '', $argument));
            if (preg_match('/^[^\-].*/', $argumentName)) {
                $this->arguments[] = $argumentName;
                continue;
            }
            
        }
    }

    public function setArgvInput(Input\ArgvInput $input) : void
    {
        $this->argvInput = $input;
    }

    public function getOption($name, $default = null)
    {
        return $this->argvInput->getOption(static::formatOptionName($name), $default);
    }

    public function hasOption($name) : bool
    {
        return $this->argvInput->hasOption(static::formatOptionName($name));
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getArguments() : array
    {
        $inputArgs = $this->argvInput->getArguments();
        array_shift($inputArgs); // Remove command name

        for ($i = 0; $i < count($this->arguments); $i++) {
            if (isset($inputArgs[$i])) {
                continue;
            }
            $inputArgs[] = readline("{$this->arguments[$i]}: ");
        }

        return $inputArgs;
    }

    public static function formatOptionName($name) : string
    {
        if (!preg_match('/^\-\-.*/', $name)) {
            $optionName = '--'.$name;
        } else {
            $optionName = $name;
        }
        return $optionName;
    }
}