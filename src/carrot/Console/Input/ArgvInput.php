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

        $this->tokens = $argv;
        $this->readToken();
    }

    protected function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function getFirstArgument() : ?string
    {
        return $this->tokens[0] ?? null;
    }
    
    public function getFromSecondArgument() : array
    {
        return $this->getArgumentsFromIndex(1);
    }

    public function getArgumentsFromIndex(int $index) : array 
    {
        $countToken = count($this->tokens);
        if ($countToken == 1) return [];

        return array_slice($this->tokens, $index, $countToken-1);
    }

    public function readToken() : void
    {
        $optionAliases = [];
        if (\file_exists(CONFIG_PATH.'/option_aliases.php')) {
            $optionAliases = include_once(CONFIG_PATH.'/option_aliases.php');
        }
        foreach ($this->tokens as $stringPattern) {
            if (self::isOption($stringPattern)) {
                $this->addOptionsByPattern($stringPattern);
                continue;
            }
            if (self::isOptionAlias($stringPattern)) {
                if (array_key_exists($stringPattern, $optionAliases)) {
                    $this->addOptionsByPattern($optionAliases[$stringPattern]);
                }
                continue;
            }
            $this->arguments[] = trim($stringPattern);
        }
    }

    public static function isOption(string $argument) : bool
    {
        return \preg_match('/\-{2}.*=*.*/', $argument);
    }

    public static function isOptionAlias(string $argument) : bool
    {
        return \preg_match('/^\-[a-z]$/', $argument);
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
