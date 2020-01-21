<?php
use App\Task;
$default =  [
    'set_token' => Task\SetTokenTask::class,
    // Refund
    'refund/create_for_canceled_order' => Task\Refund\CreateForCanceledOrderTask::class,

    // Reindex
    'order/reindex' => Task\Order\ReindexTask::class,
    'order/view' => Task\Order\ViewTask::class,
];

if (file_exists(__DIR__.'/my_alias.php')) {
    $personalConfig = require_once(__DIR__.'/my_alias.php');
    $alias = array_merge($default, $personalConfig);
} else {
    $alias = $default;
}

return $alias;
