<?php

return [
    'route_prefix' => env('APIE_PREFIX', 'apie'),

    'default_level' => env('APIE_DEFAULT_LEVEL', 's'),

    'models_path' => env('APIE_MODELS_PATH', 'App\\'),

    'models' => ['Product', 'Category', 'Brand', 'Review', 'Stock'],
];
