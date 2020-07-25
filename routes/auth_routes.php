<?php

$router->post(
    '/auth/generate',
    [
        'uses' => 'AuthGenerateController@process',
        'validator' => 'App\Domains\Auth\Http\Validators\AuthGenerateValidator'
    ]
);
