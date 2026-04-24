<?php

return [
    'paths' => ['api/*'],  // Ne mettez pas '*', spécifiez les chemins

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN'
    ],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,  // Mettez false temporairement
];
