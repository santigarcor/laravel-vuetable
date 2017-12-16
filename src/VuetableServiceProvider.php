<?php

namespace Vuetable;

use Illuminate\Support\ServiceProvider;

class VuetableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('vuetable', Vuetable::class);
        $this->app->singleton('vuetable', function () {
            return new Vuetable(app('request'));
        });
    }
}
