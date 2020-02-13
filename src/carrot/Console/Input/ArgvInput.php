<?php
namespace Carrot\Console\Input;

class ArgvInput implements InputInterface
{
    private $tokens = [];

    protected $arguments = [];
    protected $options = [];

    public function __construct(array $argv = null) 
    {
        if (is_null($argv)) {
            $argv = $_SERVER['argv'];
        }

        // strip application name
        array_shift($argv);

        $this->token = $argv;
        $this->readToken();
    }

    protected function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function getFirstArgument() : ?string
    {
        return $this->token[0] ?? null;
    }
    
    public function getFromSecondArgument() : array
    {
        return $this->getArgumentsFromIndex(1);
    }

    public function getArgumentsFromIndex(int $index) : array 
    {
        $countToken = count($this->token);
        if ($countToken == 1) return [];

        return array_slice($this->token, $index, $countToken-1);
    }

    public function readToken() : void
    {
        foreach ($this->token as $stringPattern) {
            if (self::isOption($stringPattern)) {
                $this->addOptionsByPattern($stringPattern);
                continue;
            }
            $this->arguments[] = trim($stringPattern);
        }
    }

    public static function isOption(string $argument) : bool
    {
        return \preg_match('/\-{2}.*=*.*/', $argument);
    }

    protected function addOptionsByPattern(string $stringPattern) : void
    {
        list($key, $value) = explode('=', $stringPattern);
        
        $this->options[$key] = empty($value) ? true : $value;
    }

    public function getArgumentByIndex(int $i) {
        return $this->arguments[$i] ?? null;
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }

    public function getOption($key, $default = null) {
        return $this->options[$key] ?? $default;
    }

    public function hasOption($key) : bool
    {
        return isset($this->options[$key]);
    }
}