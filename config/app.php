<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => 'en',
    'fallback_locale' => 'en',
    'jwt_app_secret' => env('JWT_APP_SECRET'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];
