<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        // позже: 'https://aves.life'
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
