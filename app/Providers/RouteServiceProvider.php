<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $fs = new Filesystem(base_path('routes'));
        $files = $fs->allFiles(realpath(base_path('routes')));
        foreach ($files as $path) {
            require $path->getRealPath();
        }
    }
}
