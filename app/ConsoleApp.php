<?php
namespace App;

class ConsoleApp
{
    public static $alias = [];
    public function __construct()
    {
        $this->initialize();
    }

    private function initialize() {
        static::$alias = require_once(CONFIG_PATH.'/console_task_alias.php');
    }

    public function handle(array $arguments) {
        $handler = $this->getHandler($arguments[1] ?? '');
        $handler->exec();
    }

    private function getHandler(string $taskAlias) : Task\Task
    {
        if (empty($taskAlias)) {
            return new Task\GuidelineTask;
        }

        if ($taskAlias == 'test' && class_exists(Task\TestTask::class)) {
            $handlerInnstance = new Task\TestTask;
            return $handlerInnstance;
        }

        $handlerTaskClass = static::$alias[$taskAlias] ?? null;
        if (is_null($handlerTaskClass)) {
            $handlerTaskClass =  '\App\Task';
            $taskPartenChunk = explode('/', $taskAlias);
            foreach ($taskPartenChunk as $chunk) {
                $handlerTaskClass .= '\\'.ucfirst(string_camelize($chunk));
            }
            $handlerTaskClass.= 'Task';
            if (!class_exists($handlerTaskClass)) {
                throw new \Exception('No Handler avaiable');
            }
        }

        $handlerInnstance = new $handlerTaskClass;
        return $handlerInnstance;
    }
}