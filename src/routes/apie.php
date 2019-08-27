<?php

$router->get('/search/{table}', 'ApieController@search');
$router->get('/search', 'ApieController@globalSearch');

$router->get('/{table}', 'ApieController@resource');
$router->get('/{table}/{id}', 'ApieController@resource');


$router->get('', 'ApieController@documentation');
