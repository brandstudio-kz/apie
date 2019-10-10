<?php

return [
    'route_middlewares' => [],
    'route_prefix' => env('APIE_PREFIX', 'apie'),
    'documentation_route' => env('APIE_DOCUMENTATION', 'documentation'),

    'default_level' => env('APIE_DEFAULT_LEVEL', 's'),

    'models_path' => env('APIE_MODELS_PATH', 'App\\'),
    'models' => [],
];
