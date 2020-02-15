<?php
namespace Carrot\Console;

use LucidFrame\Console\ConsoleTable;

class ManualRender
{
    public $registedCommand;

    public function __construct(array $commands) {
        $this->registedCommand = $commands;
    }

    public function render()
    {
        $this->renderLogo();

        $table = new ConsoleTable;
        $table->setHeaders(['Command', 'Pattern']);
        foreach ($this->registedCommand as $name => $commandHandler) {
            if ($name != $commandHandler->getName()) { // Ignore local alias
                continue;
            }
            $table->addRow([
                app('console_color')->apply(['yellow'], $commandHandler->getName()),
                $commandHandler->getPattern(),
            ]);
        }
        $table->hideBorder()->display();
    }

    public function renderLogo()
    {
        echo <<<EOD
        ╔═╗┌─┐┬─┐┬─┐┌─┐┌┬┐
        ║  ├─┤├┬┘├┬┘│ │ │ 
        ╚═╝┴ ┴┴└─┴└─└─┘ ┴ 

        My bash scripts just got so much cooler.



EOD;
    }
}