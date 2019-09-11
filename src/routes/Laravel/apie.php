<?php

Route::group([
    'namespace' => 'BrandStudio\Apie\Http\Controllers\Laravel',
    'prefix' => config('apie.route_prefix')
], function() {
    Route::get('/{table}', 'ApieController@index');
    Route::post('/{table}', 'ApieController@store');
    Route::get('/{table}/{id}', 'ApieController@show');
    Route::put('/{table}/{id}', 'ApieController@update');
    Route::delete('/{table}/{id}', 'ApieController@destroy');

    Route::get(config('apie.documentation_route'), 'ApieController@documentation');

    Route::get('/search/{table}', 'ApieController@search');
    Route::get('/search', 'ApieController@globalSearch');
});
