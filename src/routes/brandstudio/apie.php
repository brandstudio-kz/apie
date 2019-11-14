<?php

use Illuminate\Support\Str;

Route::group([
    'namespace' => 'BrandStudio\Apie\Http\Controllers',
    'prefix' => config('brandstudio.apie.route_prefix'),
    'middleware' => config('brandstudio.apie.route_middlewares'),
], function() {
    Route::get(config('brandstudio.apie.documentation_route'), 'ApieController@documentation');
    Route::get(config('brandstudio.apie.documentation_route').'/raw', 'ApieController@documentationRaw');

    $models = array_map(function($model) {
        $model = explode('\\', $model);
        return strtolower(end($model));
    }, config('brandstudio.apie.models'));

    $models = array_merge(
        $models,
        array_map(function($item) {
            return Str::plural($item);
        }, $models)
    );

    $models = implode('|', $models);

    // Search
    Route::get('search/{table}', 'ApieController@search')->where(['table' => $models]);
    Route::get('search', 'ApieController@globalSearch');

    // Rest
    Route::get('{table}', 'ApieController@index')->where(['table' => $models]);
    Route::post('{table}', 'ApieController@store')->where(['table' => $models]);
    Route::get('{table}/{id}', 'ApieController@show')->where(['table' => $models, 'id' => '[0-9]+']);
    Route::put('{table}/{id}', 'ApieController@update')->where(['table' => $models, 'id' => '[0-9]+']);
    Route::delete('{table}/{id}', 'ApieController@delete')->where(['table' => $models, 'id' => '[0-9]+']);
});
