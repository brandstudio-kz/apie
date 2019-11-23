<?php

namespace BrandStudio\Apie\Traits\Operations;

trait Insert
{

    public static function applyInsert($class, array $data)
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

                if ($relation_query instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {

                    $related_model_data = [];
                    $pivot_columns = [];
                    $pivot_relations = [];
                    $pivot_class = $relation_query->getPivotClass();
                    $related_pivot_key_name = $relation_query->getRelatedPivotKeyName();
                    $foreign_pivot_key_name = $relation_query->getForeignPivotKeyName();
                    $relation_columns = $relation_query->getPivotColumns();
                    $is_default_pivot = ($pivot_class == 'Illuminate\Database\Eloquent\Relations\Pivot');

                    foreach($value as $key => $data) {
                        if (!$is_default_pivot && method_exists($pivot_class, $key)) {
                            $pivot_relations[$key] = $data;
                        } else if (in_array($key, $relation_columns)) {
                            $pivot_columns[$key] = $data;
                        } else {
                            $related_model_data[$key] = $data;
                        }
                    }

                    $relation = static::applyInsert($relation_class, $related_model_data);

                    if (!$is_default_pivot) {
                        $pivot = $pivot_class::create(array_merge(
                            $pivot_columns,
                            [
                                $related_pivot_key_name => $relation->{$relation_query->getRelatedKeyName()},
                                $foreign_pivot_key_name => $model->{$relation_query->getParentKeyName()},
                            ]
                        ));
                        static::createRelations($pivot, $pivot_relations);
                    } else {
                        $relation_query->attach($relation->getKey(), $pivot_columns);
                    }

                } else {
                    $relation = static::applyInsert($relation_class, $value);
                    $relation_query->attach($relation->getKey());
                }
            }
        }

    }

}
