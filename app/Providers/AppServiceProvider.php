<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'local') {
            try {
                opcache_reset();
            } catch (Throwable $throw) {
                Log::warning('warning opcache is disabled');
            }
        }
    }
}
