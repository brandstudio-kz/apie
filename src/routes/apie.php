<?php

Route::group([
    'namespace' => 'BrandStudio\Apie\Http\Controllers',
    'prefix' => config('apie.route_prefix')
], function() {

    Route::get(config('apie.documentation_route'), 'ApieController@documentation');
    Route::get(config('apie.documentation_route').'/raw', 'ApieController@documentationRaw');

    Route::get('search/{table}', 'ApieController@search');
    Route::get('search', 'ApieController@globalSearch');

    Route::get('{table}', 'ApieController@index');
    Route::post('{table}', 'ApieController@store');
    Route::get('{table}/{id}', 'ApieController@show');
    Route::put('{table}/{id}', 'ApieController@update');
    Route::delete('{table}/{id}', 'ApieController@destroy');

});
