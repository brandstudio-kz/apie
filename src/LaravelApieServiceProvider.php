<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class LaravelApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/apie.php', 'brandstudio.recaptcha'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/Laravel/routes.php');

        $this->publishes([
            __DIR__.'/config/recaptcha.php' => config_path('brandstudio/recaptcha.php')
        ], 'config');
    }

}
