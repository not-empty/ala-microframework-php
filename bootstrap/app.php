<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
*/

$app->configure('app');
$app->configure('newRelic');
$app->configure('token');
$app->configure('version');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
*/

$app->routeMiddleware([
    'auth' => App\Http\Middlewares\AuthenticateJwt::class,
    'filters' => App\Http\Middlewares\RequestFilters::class,
    'parameter' => App\Http\Middlewares\RequestParameters::class,
    'start' => App\Http\Middlewares\RequestStart::class,
    'validator' => App\Http\Middlewares\RequestValidator::class,
]);

$app->middleware([
    App\Http\Middlewares\Cors::class,
    App\Http\Middlewares\NewRelicLumen::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\NewRelicServiceProvider::class);
$app->register(App\Providers\RouteServiceProvider::class);

return $app;
