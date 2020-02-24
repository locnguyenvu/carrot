<?php
namespace Carrot\Console;

use Carrot\Exception as CarrotException;

class Application
{
    public $commands = [];
    public $aliases = [];

    private $commandAliases = null;

    private $initialize = false;

    public function __construct()
    {
        $this->container = new \Carrot\Container();
        $this->commandAliases = array_flip(include_once(CONFIG_PATH.'/command_aliases.php'));
        $this->loadCommand();
    }

    private function loadCommand() {
        $consoleBootstrapConfig = require_once(BOOTSTRAP_PATH.'/console.php');
        $commands = array_get($consoleBootstrapConfig, 'commands');
        foreach ($commands as $command) {
            $this->add(new $command);
        }
    }

    public function add(Command $command) : void
    {
        $command->setApplication($this);

        $name = $command->getName();
        $this->commands[$name] = $command;

        if (array_key_exists($name, $this->commandAliases)) {
            $commandAlias = $this->commandAliases[$name];
            $this->commands[$commandAlias] = $command;
        }
    }

    public function run()
    {
        $argvInput = new Input\ArgvInput();

        $commandName = $argvInput->getFirstArgument();
        if ($commandName === null) {
            $manual = new ManualRender($this->commands);
            $manual->render();
            return;
        }

        if (!array_key_exists($commandName, $this->commands)) {
            throw new \Carrot\Exception\InvalidCommandException();
        }

        $handler = $this->commands[$commandName];
        try {
            $handler->run($argvInput);
        } catch (CarrotException\Http\HttpException $e) {
            echo $e->getMessage();
        }
    }

    public function getDI() : \Carrot\DI
    {
        return $this->container::$di;
    }

    public function getService(string $key)
    {
        return $this->container::$di->get($key);
    }

    private function printHelper()
    {
        foreach ($this->commands as $key => $command) {
            if ($key != $command->getName()) continue;
            echo app('console_color')->apply(['yellow'], $command->getName()).'>>>'.$command->getPattern().PHP_EOL;
        }
    }

}