<?php
namespace App\Task;

interface Task
{
    public function exec() : void;

    public function readArguments(array $shellArguments) : void;
}