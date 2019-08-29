<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class LumenApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->router->group([
            'namespace' => 'BrandStudio\Apie\Http\Controllers\Lumen',
            'prefix' => config('apie.route_prefix'),
            'middleware' => config('apie.route_middlewares'),
        ], function ($router) {
            include __DIR__.'/routes/Lumen/apie.php';
        });

        $this->app->make('BrandStudio\Apie\Http\Controllers\Lumen\ApieController');
    }

    public function boot()
    {
        //
    }

}
