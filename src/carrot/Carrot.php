<?php
namespace Carrot;

class Carrot
{
    /**
     * Print console error with error
     */
    public static function printError(string $message, bool $newline = true) : void
    {
        $colorConsole = new \JakubOnderka\PhpConsoleColor\ConsoleColor;
        $template = sprintf("[!] Error: %s\n",$message);
        if ($newline) {
            $content = PHP_EOL.$template; 
        } else {
            $content = $template;
        }
        echo $colorConsole->apply(['red'], $content);
    }

    /**
     * Print console error with error
     */
    public static function printSuccess(string $message, bool $newline = true) : void
    {
        $colorConsole = new \JakubOnderka\PhpConsoleColor\ConsoleColor;
        $template = sprintf("Success: %s\n", $message);
        if ($newline) {
            $content = PHP_EOL.$template; 
        } else {
            $content = $template;
        }
        echo $colorConsole->apply(['green'], $content);
    }
}