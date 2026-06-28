<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Allows the portfolio site (thembrk.github.io) and any local dev
    | environment to call the API. Update allowed_origins in production
    | if you want to restrict access further.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://thembrk.github.io',
        'http://localhost:5173',
        'http://localhost:8080',
        'http://127.0.0.1:5500',
    ],

    'allowed_origins_patterns' => [
        // Allow any localhost port for local dev
        '/^http:\/\/localhost(:\d+)?$/',
        '/^http:\/\/127\.0\.0\.1(:\d+)?$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
