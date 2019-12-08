<?php
use App\Task;
return [
    'settoken' => Task\SetTokenTask::class,
    'refund/cfco' => Task\Refund\CreateForCanceledOrderTask::class,
];