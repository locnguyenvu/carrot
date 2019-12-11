<?php
namespace App\Task;

abstract class AbstractTask 
{
    protected $arguments = [];

    public function __construct() {
        $this->initialize();
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