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
        \App\Console\Order\ListEventCommand::class,

        // Refund
        \App\Console\Refund\CreateForCaneledOrderCommand::class,
        \App\Console\Refund\ListByOrderCommand::class,
        \App\Console\Refund\ViewCommand::class,
        \App\Console\Refund\BulkViewCommand::class,

        // Product
        \App\Console\Product\BulkViewCommand::class,

        // Utils
        \App\Console\Utils\UuidCommand::class,

        // Sql Generator
        \App\Console\SqlGenerator\TransferOrderPaymentTransactionCommand::class 
    ]
];
