<?php

namespace BrandStudio\Apie\Traits\Operations;

trait Insert
{

    public static function applyInsert($class, &$query, array $data)
    {
        $relations = [];

        foreach($data as $key => $value) {
            if (method_exists($class, $key)) {
                $relations[$key]=$value;
                unset($data[$key]);
            }
        }

        $model = $class::firstOrCreate($data);
        static::createRelations($model, $relations);

        return $model;
    }

    public static function createRelations($model, array $relations, $relation = null)
    {
        foreach($relations as $relation_key => $value) {
            if (is_array($value) && (array_keys($value) === range(0, count($value) - 1) || array() === $value)) {// If array is not associative
                static::createRelations($model, $value, $relation_key);
            } else {
                $relation_name = $relation ? $relation : $relation_key;
                $relation_query = $model->{$relation_name}();
                $relation_class = get_class($relation_query->getModel());
                $relation = static::applyInsert($relation_class, $relation_query, $value);
                $model->{$relation_name}()->attach($relation->id);
            }
        }

    }

}
