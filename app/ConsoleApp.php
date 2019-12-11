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
        $task = $this->getTaskHandler($arguments[1] ?? '');
        $taskArguments = array_slice($arguments, 2);
        $task->readArguments($taskArguments);
        $task->exec();
    }

    private function getTaskHandler(string $taskAlias) : Task\Task
    {
        if (empty($taskAlias)) {
            return new Task\GuidelineTask;
        }

        if ($taskAlias == 'test' && class_exists(Task\TestTask::class)) {
            $taskInstance = new Task\TestTask;
            return $taskInstance;
        }

        $taskHandlerClass = static::$alias[$taskAlias] ?? null;
        if (is_null($taskHandlerClass)) {
            $taskHandlerClass =  '\App\Task';
            $taskPartenChunk = explode('/', $taskAlias);
            foreach ($taskPartenChunk as $chunk) {
                $taskHandlerClass .= '\\'.ucfirst(string_camelize($chunk));
            }
            $taskHandlerClass.= 'Task';
            if (!class_exists($taskHandlerClass)) {
                throw new \Exception('No Handler avaiable');
            }
        }

        $taskInstance = new $taskHandlerClass;
        return $taskInstance;
    }
}