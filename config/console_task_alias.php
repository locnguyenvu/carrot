<?php
use App\Task;
return [
    'stt' => Task\SetTokenTask::class,
    'rcfco' => Task\Refund\CreateForCanceledOrderTask::class,
];
