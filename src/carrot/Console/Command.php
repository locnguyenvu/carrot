<?php

namespace Carrot\Console;

class Command
{
    protected $app;
    protected $parser;
    protected $result;

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

    public function getExportJsonName() : string
    {
        return str_replace([':', '-'], '_', $this->getName()).time().'.json';
    }

    public function run(Input\InputInterface $argvInput) : void
    {
        $this->parser->setArgvInput($argvInput);
        \call_user_func_array([$this, 'exec'], $this->parser->getArguments());

        if ($this->getOption('exportJson')) {
            $exportFilePath = ROOT_PATH.'/exports/'.$this->getExportJsonName();
            \file_put_contents($exportFilePath, json_encode($this->result, JSON_PRETTY_PRINT));

            echo "\n\nExpot file: {$exportFilePath}";
        }
    }

    public function hasOption(string $optionName) :bool 
    {
        return $this->parser->hasOption($optionName);
    }

    public function getOption(string $optionName) 
    {
        return $this->parser->getOption($optionName, false);
    }
}