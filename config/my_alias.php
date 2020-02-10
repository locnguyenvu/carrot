<?php
use App\Task;
return [
    'stt' => Task\SetTokenTask::class,
    'rcfco' => Task\Refund\CreateForCanceledOrderTask::class,
    'rlbo' => Task\Refund\ListByOrderCodeTask::class,
    'or' => Task\Order\ReindexTask::class,
    'ov' => Task\Order\ViewTask::class,
];
