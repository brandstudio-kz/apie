<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class LaravelApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/apie.php', 'apie'
        );
        // dd(config('apie'));
        // $this->app->make('BrandStudio\Apie\Http\Controllers\Laravel\ApieController');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/Laravel/apie.php');

        $this->publishes([
            __DIR__.'/config/apie.php' => config_path('apie.php')
        ], 'config');
    }

}
