<?php

return [
    'host' => env('DB_CACHE_HOST', 'localhost'),
    'port' => env('DB_CACHE_PORT', 6379),
    'enable' => env('DB_CACHE', false),
];
