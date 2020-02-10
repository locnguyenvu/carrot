<?php
namespace Carrot\Console;

class Application
{
    public $commands = [];
    public $aliases = [];

    private $localAliases = null;

    private $initialize = false;

    public function __construct()
    {
        $this->container = new \Carrot\Container();
        $this->localAliases = array_flip(include_once(CONFIG_PATH.'/local_aliases.php'));
    }

    public function add(Command $command) : void
    {
        $command->setApplication($this);

        $name = $command->getName();
        $this->commands[$name] = $command;

        $commandClassPath = get_class($command);
        if (array_key_exists($name, $this->localAliases)) {
            $commandAlias = $this->localAliases[$name];
            $this->commands[$commandAlias] = $command;
        }
    }

    public function run()
    {
        $argvInput = new Input\ArgvInput();

        $commandName = $argvInput->getFirstArgument();

        if (!array_key_exists($commandName, $this->commands)) {
            throw new \Carrot\Exception\InvalidCommandException();
        }

        $handler = $this->commands[$commandName];
        try {
            $handler->run($argvInput);
        } catch (\Tikivn\Exception\ApiException $e) {
            echo json_encode($e->getDetailAsArray(), JSON_PRETTY_PRINT);
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

}