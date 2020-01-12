<?php
namespace App\Task;

class GuidelineTask implements Task
{
    public function exec() : void
    {
        printf("Task alias config file: config/console_task_alias.php

\e[0;32msettoken\e[0m                                Set token 

\e[0;33mRefund:\e[0m
    \e[0;32mrefund/create_for_canceled_order\e[0m    Create refundd for canceld order


\e[0;33mOrder:\e[0m
    \e[0;32morder/reindex\e[0m                       Reindex order
        ");
    }

    public function readArguments(array $shellArguments) : void
    {
        return;
    }

}