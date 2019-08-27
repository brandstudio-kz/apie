<?php

namespace BrandStudio\Apie\Traits;

trait ApieModelTrait
{

    public static function getApieLevel(string $level) : array
    {
        return static::getApieLevels()[$level];
    }

    public static function getApieLevelAttributeNames($level) : array
    {
        return array_map(
            function($attribute) {
                return trim(explode(':', $attribute)[0]);
            },
            static::getApieLevel($level)
        );
    }

    public static function getApieRelation($relation) : array
    {
        return static::getApieRelations()[$relation];
    }

    private function getApieLevelRelationsQuery(string $levels, array $relation_stack) : array
    {
        $index = 1;
        $relations = static::getApieLevelRelations($levels[0]);
        $relations_query = [];

        foreach($relations as $relation) {
            $relation_key = $this->getTable()."_".$relation;
            if (count($relation_stack)>8) {
                dd($relation_stack);
            }
            if (!in_array($relation_key, $relation_stack)) {
                $relations_query[$relation] = function($query) use ($index, $levels, $relation_key, $relation_stack) {
                    $query->level(
                        $levels[$index++] ?? $levels[0],
                        array_merge(
                            $relation_stack,
                            [$relation_key]
                        )
                    );
                };
            }
        }
        return $relations_query;
    }

    private static function getApieLevelAttributes(string $level) : array
    {
        return array_values(
            array_filter(
                static::getApieLevelAttributeNames($level),
                function($attribute) {
                    return !static::isApieRelation($attribute);
                }
            )
        );
    }

    private static function getApieLevelRelations(string $level) : array
    {
        return array_values(
            array_filter(
                static::getApieLevelAttributeNames($level),
                function($attribute) {
                    return static::isApieRelation($attribute);
                }
            )
        );
    }

    private static function isApieRelation(string $attribute) : bool
    {
        return in_array($attribute, static::getApieRelations());
    }

    private function getApieKeys() : array
    {
        return array_values(
            preg_grep(
                "/id$/",
                $this->getConnection()
                     ->getSchemaBuilder()
                     ->getColumnListing($this->getTable())
            )
        );
    }

}
