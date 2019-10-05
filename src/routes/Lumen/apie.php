<?php

$router->get(config('apie.documentation_route'), 'ApieController@documentation');
$router->get(config('apie.documentation_route').'/raw', 'ApieController@documentationRaw');


$router->get('/search/{table}', 'ApieController@search');
$router->get('/search', 'ApieController@globalSearch');

$router->get('/{table}', 'ApieController@index');
$router->post('/{table}', 'ApieController@store');
$router->get('/{table}/{id}', 'ApieController@show');
$router->put('/{table}/{id}', 'ApieController@update');
$router->delete('/{table}/{id}', 'ApieController@destroy');
