<?php
namespace App\Task;

abstract class AbstractTask 
{
    public function __construct() {
        $this->initialize();
    }

    protected function initialize()
    {
        
    }
}