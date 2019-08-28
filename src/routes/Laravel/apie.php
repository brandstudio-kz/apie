<?php

Route::get('/search/{table}', 'ApieController@search');
Route::get('/search', 'ApieController@globalSearch');

Route::get('/{table}', 'ApieController@resource');
Route::get('/{table}/{id}', 'ApieController@resource');

Route::get(config('apie.documentation_route'), 'ApieController@documentation');
