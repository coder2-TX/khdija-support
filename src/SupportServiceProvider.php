<?php

namespace Khdija\Support;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SupportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // نشر migrations - المسار الصحيح
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'khdija-support-migrations');

        // نشر views - المسار الصحيح
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/khdija-support'),
        ], 'khdija-support-views');

        // نشر config - المسار الصحيح
        $this->publishes([
            __DIR__.'/../../config/khdija-support.php' => config_path('khdija-support.php'),
        ], 'khdija-support-config');

        // تحميل routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        // تحميل views - المسار الصحيح
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'khdija-support');

        // تحميل migrations - المسار الصحيح
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function register()
    {
        // دمج config - المسار الصحيح
        $this->mergeConfigFrom(
            __DIR__.'/../../config/khdija-support.php', 'khdija-support'
        );
    }
}