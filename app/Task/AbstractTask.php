<?php
namespace App\Task;

use Carrot\Carrot;

abstract class AbstractTask 
{
    protected $arguments = [];

    public function __construct() {
        $this->initialize();

        $description = $this->description();
        if (!is_null($description)) {
            Carrot::printWithColor($description, 'blue', true);
        }
    }

    protected function description() {
        return null;
    }

    protected function initialize()
    {
        
    }

    public function readArguments(array $shellArguments) : void
    {
        foreach ($shellArguments as $arg) {
            if (preg_match('/^[a-z]+=.*/', $arg)) {
                list($key, $value) = explode('=', $arg);
                $this->arguments[$key] = $value;
            }
        }
    }

    public function hasArgument($key) : bool 
    {
        return isset($this->arguments[$key]);
    }

    public function getArgument($key)
    {
        return $this->arguments[$key] ?? null;
    }
}