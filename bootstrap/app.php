<?php

return [
    'services' => [
        'console_color' => [
            'class' => \JakubOnderka\PhpConsoleColor\ConsoleColor::class,
        ],
        'access_token' => [
            'class' => \Tikivn\Authentication\AccessToken::class,
        ],
        'oms_client' => [
            'class' => \Tikivn\Oms\HttpClient::class,
        ],
        'orderRepository' => [
            'class' => \Tikivn\Oms\Order\Repository::class
        ],
        'refundRepository' => [
            'class' => \Tikivn\Oms\Refund\Repository::class
        ],
        'pegasus_client' => [
            'class' => \Tikivn\Pegasus\HttpClient::class
        ],
        'catalogRepository' => [
            'class' => \Tikivn\Pegasus\Catalog\Repository::class
        ]
    ],
];