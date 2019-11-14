<?php

namespace BrandStudio\Apie;

use Illuminate\Support\Str;
use BrandStudio\Apie\Query;

class Apie
{

    public static function model(string $model)
    {
        return new Query(static::getModelClass($model));
    }

    private static function getModelClass($model)
    {
        foreach(config('brandstudio.apie.models') as $item) {
            $model_chunks = explode("\\", $item);
            if (strtolower(end($model_chunks)) == $model) {
                return $item;
            }
        }
        abort(404);
    }

}
