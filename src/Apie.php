<?php

namespace BrandStudio\Apie;

use Illuminate\Support\Str;
use BrandStudio\Apie\Query;

class Apie
{

    public static function model(string $model)
    {
        if (!class_exists($model)) {
            $model = static::getModelClass($model);
        }
        return new Query($model);
    }

    private static function getModelClass($model)
    {
        foreach(config('brandstudio.apie.models') as $item) {
            if (strtolower(class_basename($item)) == $model) {
                return $item;
            }
        }
        abort(404);
    }

}
