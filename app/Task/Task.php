<?php
namespace App\Task;

interface Task
{
    public function exec() : void;
}