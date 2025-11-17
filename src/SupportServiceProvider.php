<?php

namespace Khdija\Support;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SupportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'khdija-support-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/khdija-support'),
        ], 'khdija-support-views');

        // Publish config
        $this->publishes([
            __DIR__.'/../../config/khdija-support.php' => config_path('khdija-support.php'),
        ], 'khdija-support-config');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'khdija-support');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/khdija-support.php', 'khdija-support'
        );
    }
}