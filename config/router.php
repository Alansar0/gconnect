<?php

return [
    'use_mock' => env('ROUTER_API_MOCK', true),

    'connection' => [
        'host' => env('ROUTER_HOST', '192.168.88.1'),
        'username' => env('ROUTER_USER', 'admin'),
        'password' => env('ROUTER_PASS', ''),
        'port' => env('ROUTER_PORT', 8728),
    ],
];
