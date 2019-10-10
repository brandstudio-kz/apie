<?php

Route::group([
    'namespace' => 'BrandStudio\Apie\Http\Controllers',
    'prefix' => config('apie.route_prefix')
], function() {

    $models = array_map('strtolower', config('apie.models'));
    $models = array_merge(
        $models,
        array_map(function($item) {
            return Str::plural($item);
        }, $models)
    );
    $models = implode('|', $models);

    Route::get(config('apie.documentation_route'), 'ApieController@documentation');
    Route::get(config('apie.documentation_route').'/raw', 'ApieController@documentationRaw');

    Route::get('search/{table}', 'ApieController@search')->where(['table' => $models]);
    Route::get('search', 'ApieController@globalSearch');

    Route::get('{table}', 'ApieController@index')->where(['table' => $models]);
    Route::post('{table}', 'ApieController@store')->where(['table' => $models]);
    Route::get('{table}/{id}', 'ApieController@show')->where(['table' => $models, 'id' => '[0-9]+']);
    Route::put('{table}/{id}', 'ApieController@update')->where(['table' => $models, 'id' => '[0-9]+']);
    Route::delete('{table}/{id}', 'ApieController@destroy')->where(['table' => $models, 'id' => '[0-9]+']);

});
