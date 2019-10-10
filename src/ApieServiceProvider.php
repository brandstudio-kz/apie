<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class ApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/apie.php', 'apie'
        );
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'brandstudio');
        $this->loadRoutesFrom(__DIR__.'/routes/apie.php');

        $this->publishes([
            __DIR__.'/config/apie.php' => config_path('apie.php')
        ], 'config');
    }

}
