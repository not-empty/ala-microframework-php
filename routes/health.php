<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [
    'uses' => 'App\Domains\Health\Http\Controllers\HealthApiController@process',
]);

Route::get('health/key', function () {
    return \Illuminate\Support\Str::random(32);
});

Route::get('health', [
    'uses' => 'App\Domains\Health\Http\Controllers\HealthApiController@process',
]);
