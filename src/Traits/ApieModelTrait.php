<?php

namespace BrandStudio\Apie\Traits;

trait ApieModelTrait
{

    public static function apieQuery()
    {
        return static::query();
    }


    public function scopeLevel($query, string $levels, array $relation_stack = [])
    {
        $attributes = array_merge(
            $this->getApieKeys(),
            static::getApieLevelAttributes($levels[0])
        );

        $relations =$this->getApieLevelRelationsQuery($levels, $relation_stack);

        $query->select($attributes)->with($relations);
    }

    public static function getApieLevelsParsed() : array
    {
        $levels = static::getApieLevels();

        foreach($levels as $level=>$values) {
            $level_data = [];
            foreach($values as $value) {
                $raw = array_map('trim', explode(':', $value));
                $name = $raw[0];
                $type = $raw[1] ?? 'string';

                preg_match('/^(.*)\[(.*)\]$/', $type, $match);
                if ($match) {
                    $type = $match[1];
                    $level_data[$name]['model'] = $match[2];
                }

                $level_data[$name]['type'] = $type;
            }
            $levels[$level] = $level_data;
        }
        return $levels;
    }

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
            $relation_key = [$this->getTable(), str_plural($relation)];
            sort($relation_key);
            $relation_key = implode('_', $relation_key);
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
