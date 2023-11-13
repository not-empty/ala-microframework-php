<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intouch\Newrelic\Newrelic;

class NewRelicServiceProvider extends ServiceProvider
{

    /**
     * register the service provider
     */
    public function register()
    {
        $this->app->singleton(Newrelic::class, function () {
            return new Newrelic();
        });

        if ($this->app instanceof \Laravel\Lumen\Application) {
            $this->app->configure('newRelic');
        }
    }
}
