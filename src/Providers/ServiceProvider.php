<?php

namespace Elnooronline\LaravelSettings\Providers;

use Elnooronline\LaravelSettings\SettingBuilder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-settings.php', 'laravel_settings');
        $this->publishes([__DIR__.'/../../config/laravel-settings.php' => config_path('laravel-settings.php')], 'settings:config');
        $this->publishes([__DIR__.'/../../migrations' => database_path('migrations')], 'settings:migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('settings', function () {
           return new SettingBuilder();
        });
    }
}
