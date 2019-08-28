<?php

Route::group([
    'namespace' => 'BrandStudio\Apie\Http\Controllers\Laravel',
    'prefix' => config('apie.route_prefix')
], function() {
    Route::get('/search/{table}', 'ApieController@search');
    Route::get('/search', 'ApieController@globalSearch');

    Route::get('/{table}', 'ApieController@resource');
    Route::get('/{table}/{id}', 'ApieController@resource');

    Route::get(config('apie.documentation_route'), 'ApieController@documentation');
});
