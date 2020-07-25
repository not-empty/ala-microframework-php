<?php

require __DIR__.'/list_routes.php';

$listConfigs = [
    [
        'middleware' => [],
        'routes' => [
            'App\Domains\Health\Http\Controllers' => 'health',
        ],
    ],
    [
        'middleware' => [
            'start',
            'validator',
        ],
        'routes' => [
            'App\Domains\Auth\Http\Controllers' => 'auth',
        ],
    ],
    [
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ],
        'routes' => $listRoutes,
    ],
];
