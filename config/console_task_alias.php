<?php
use App\Task;
return [
    'settoken' => Task\SetTokenTask::class,
    'refund/create_for_canceled_order' => Task\Refund\CreateForCanceledOrderTask::class,
];