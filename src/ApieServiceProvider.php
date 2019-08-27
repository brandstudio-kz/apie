<?php

namespace BrandStudio\Apie;

use Illuminate\Support\ServiceProvider;

class ApieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->router->group([
            'namespace' => 'BrandStudio\Apie\Http\Controllers',
        ], function ($router) {
            include __DIR__.'/routes/apie.php';
        });

        $this->app->make('BrandStudio\Apie\Http\Controllers\ApieController');
    }

    public function boot()
    {
        //
    }

}
