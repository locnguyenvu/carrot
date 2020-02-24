<?php
namespace Carrot\Console\Traits;

trait JsonHelpTrait
{
    public function renderHelp() :void 
    {
        parent::renderHelp();
        echo 'Options:'.PHP_EOL;
        $avaiableOptions = [
            ['name' => '--filterFields', 'description' => 'Filter specific fields on result']
        ];
        foreach ($avaiableOptions as $option)
        {
            printf("  %s\t\t%s\n", $option['name'], $option['description']);
        }
    }
}
