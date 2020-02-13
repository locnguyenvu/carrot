<?php
return [
    'commands' => [
        // Access token
        \App\Console\AccessToken\SetCommand::class,

        // Order
        \App\Console\Order\ViewCommand::class,
        \App\Console\Order\BulkViewCommand::class,
        \App\Console\Order\BulkReindexCommand::class,
        \App\Console\Order\BulkChangeStatusCommand::class,
    ]
];