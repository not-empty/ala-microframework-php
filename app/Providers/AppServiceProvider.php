<?php

namespace App\Providers;

use App\Constants\PatternsConstants;
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

        Validator::extend('filter', function ($attribute, $value, $parameters, $validator) {
            if (!empty(preg_match(PatternsConstants::FILTER, $value))) {
                return true;
            }

            return false;
        });

        Validator::extend('ulid', function ($attribute, $value, $parameters, $validator) {
            if (!empty(preg_match(PatternsConstants::ULID, $value))) {
                return true;
            }

            return false;
        });
    }
}
