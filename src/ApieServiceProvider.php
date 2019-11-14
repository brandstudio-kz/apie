<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class ApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->configure();
        $this->bindings();
        $this->registerMiddlewares();

        if ($this->app->runningInConsole()) {
            $this->publish();
        }

    }

    public function boot()
    {
        $this->loadRoutes();
        $this->loadResources();

        if ($this->app->runningInConsole()) {
            $this->publish();
        }

    }

    private function configure()
    {
        $this->mergeConfigFrom(__DIR__.'/config/apie.php', 'brandstudio.apie');
    }


    private function bindings()
    {
        //
    }

    private function registerMiddlewares()
    {
        //
    }

    private function loadRoutes()
    {
        $path = '/routes/brandstudio/apie.php';
        $path = file_exists(base_path().$path) ? base_path().$path : __DIR__.$path;
        $this->loadRoutesFrom($path);
    }

    private function loadResources()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'brandstudio');
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/config/apie.php' => config_path('brandstudio/apie.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/brandstudio')
        ], 'views');

        $this->publishes([
            __DIR__.'/routes/brandstudio/' => base_path('routes/brandstudio')
        ], 'routes');
    }


}
