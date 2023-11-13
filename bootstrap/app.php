<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->configure('app');
$app->configure('newRelic');
$app->configure('suffix');
$app->configure('token');
$app->configure('version');
$app->configure('seed');

$app->routeMiddleware([
    'auth' => App\Http\Middlewares\AuthenticateJwt::class,
    'filters' => App\Http\Middlewares\RequestFilters::class,
    'parameter' => App\Http\Middlewares\RequestParameters::class,
    'start' => App\Http\Middlewares\RequestStart::class,
    'suffix' => App\Http\Middlewares\SuffixTable::class,
    'validator' => App\Http\Middlewares\RequestValidator::class,
]);

$app->middleware([
    App\Http\Middlewares\Cors::class,
    App\Http\Middlewares\NewRelicLumen::class,
]);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\NewRelicServiceProvider::class);

require __DIR__ . '/list_config.php';

foreach ($listConfigs as $i => $routeConfig) {
    $middleware = $routeConfig['middleware'];

    foreach($routeConfig['routes'] as $namespaceRoute => $fileRoute) {
        $config = [
            'namespace' => $namespaceRoute,
            'middleware' => $middleware
        ];

        $app->router->group($config, function($router) use ($fileRoute) {
            $file = __DIR__ . "/../routes/{$fileRoute}_routes.php";
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}

return $app;
