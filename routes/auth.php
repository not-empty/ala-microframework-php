<?php

use Illuminate\Support\Facades\Route;

Route::post(
    '/auth/generate',
    [
        'uses' => 'App\Domains\Auth\Http\Controllers\AuthGenerateController@process',
        'validator' => 'App\Domains\Auth\Http\Validators\AuthGenerateValidator',
        'middleware' => [
            'start',
            'validator'
        ]
    ]
);
