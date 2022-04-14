<?php

use Illuminate\Support\Facades\Route;

$patterns = [
    'id' => '[0-9A-Z]{26}',
];

Route::delete(
    "/sample/delete/{id:{$patterns['id']}}",
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleDeleteController@process',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::get(
    '/sample/dead_list',
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleDeadListController@process',
        'validator' => 'App\Domains\Sample\Http\Validators\SampleDeadListValidator',
        'parameters' => 'App\Domains\Sample\Http\Parameters\SampleParameters',
        'filters' => 'App\Domains\Sample\Http\Filters\SampleFilters',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::get(
    '/sample/list',
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleListController@process',
        'validator' => 'App\Domains\Sample\Http\Validators\SampleListValidator',
        'parameters' => 'App\Domains\Sample\Http\Parameters\SampleParameters',
        'filters' => 'App\Domains\Sample\Http\Filters\SampleFilters',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::get(
    "/sample/dead_detail/{id:{$patterns['id']}}",
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleDeadDetailController@process',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::get(
    "/sample/detail/{id:{$patterns['id']}}",
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleDetailController@process',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::patch(
    "/sample/edit/{id:{$patterns['id']}}",
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleEditController@process',
        'validator' => 'App\Domains\Sample\Http\Validators\SampleEditValidator',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::post(
    '/sample/add',
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleAddController@process',
        'validator' => 'App\Domains\Sample\Http\Validators\SampleAddValidator',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);

Route::post(
    '/sample/bulk',
    [
        'uses' => 'App\Domains\Sample\Http\Controllers\SampleBulkController@process',
        'validator' => 'App\Domains\Sample\Http\Validators\SampleBulkValidator',
        'parameters' => 'App\Domains\Sample\Http\Parameters\SampleParameters',
        'middleware' => [
            'auth',
            'start',
            'validator',
            'filters',
            'parameter',
        ]
    ]
);
