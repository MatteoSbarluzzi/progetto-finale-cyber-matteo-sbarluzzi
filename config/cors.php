<?php

return [

    'paths' => ['api/*', 'livewire/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST'],

    'allowed_origins' => [
        'http://cyber.blog:8000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
