<?php

namespace Carrot\Console;

class Command
{
    protected $app;
    protected $parser;

    protected static $pattern = null;

    public function __construct()
    {
        $this->parser = new Parser(static::$pattern);
        $this->init();
    }

    protected function init() : void {}

    public function setApplication(Application $app) : void
    {
        $this->app = $app;
    }

    public function getName() : string
    {
        return $this->parser->getName();
    }

    public function run(Input\InputInterface $argvInput) : void
    {
        $this->parser->setArgvInput($argvInput);
        \call_user_func_array([$this, 'exec'], $this->parser->getArguments());
    }
}