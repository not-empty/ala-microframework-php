<?php

$router->get(
    '/',
    [
        'uses' => 'HealthApiController@process',
    ]
);

$router->get(
    '/health',
    [
        'uses' => 'HealthApiController@process',
    ]
);

$router->get(
    '/health/key',
    function () {
        return \Illuminate\Support\Str::random(32);
    }
);
