<?php

namespace Khdija\Support;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SupportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // نشر migrations - استخدم publishes() بدلاً من publishesMigrations()
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'khdija-support-migrations');

        // نشر views
        $this->publishes([
            __DIR__.'/Resources/views' => resource_path('views/vendor/khdija-support'),
        ], 'khdija-support-views');

        // نشر config
        $this->publishes([
            __DIR__.'/../config/khdija-support.php' => config_path('khdija-support.php'),
        ], 'khdija-support-config');

        // تحميل routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        // تحميل views
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'khdija-support');

        // تحميل migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        // دمج config
        $this->mergeConfigFrom(
            __DIR__.'/../config/khdija-support.php', 'khdija-support'
        );
    }
}