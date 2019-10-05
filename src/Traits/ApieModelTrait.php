<?php

namespace BrandStudio\Apie\Traits;

trait ApieModelTrait
{

    public static function apieQuery()
    {
        return static::apie();
    }

    public static function apieShowQuery()
    {
        return static::apieQuery();
    }

    public function scopeApie($query)
    {
        //
    }

    public function scopeBeforeLevel($query, array $attributes, array $relations, array $relation_stack = [])
    {
        //
    }

    public function scopeAfterLevel($query, array $attributes, array $relations, array $relation_stack = [])
    {
        //
    }

    public function scopeLevel($query, string $levels, array $relation_stack = [])
    {
        $attributes = array_merge(
            $this->getApieKeys(),
            static::getApieLevelAttributes($levels[0])
        );

        $relations =$this->getApieLevelRelationsQuery($levels, $relation_stack);

        $query->beforeLevel($attributes, $relations, $relation_stack)
              ->select($attributes)
              ->with($relations)
              ->afterLevel($attributes, $relations, $relation_stack);
    }


    public static function getApieLevel(string $level) : array
    {
        return static::getApieLevels()[$level] ?? static::getApieLevels()[config('apie.default_level')] ?? [];
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

            $relation_class = static::getApieClass($relation);

            $relation_key = [$this->getTable(), str_plural($relation)];
            sort($relation_key);
            $relation_key = implode('_', $relation_key);

            if (!in_array($relation_key, $relation_stack)) {
                $relations_query[$relation] = function($query) use ($index, $levels, $relation_key, $relation_stack, $relation_class) {
                    $query->apie();
                    $class_name = class_basename(static::class);

                    $before_scope = "scopeBefore{$class_name}Relation";
                    $after_scope = "scopeAfter{$class_name}Relation";
                    $before = "before{$class_name}Relation";
                    $after = "after{$class_name}Relation";

                    if (class_exists($relation_class) && method_exists(new $relation_class, $before_scope)) {
                        $query->{$before}();
                    }

                    $query->level(
                        $levels[$index++] ?? $levels[0],
                        array_merge(
                            $relation_stack,
                            [$relation_key]
                        )
                    );
                    if (class_exists($relation_class) && method_exists(new $relation_class, $after_scope)) {
                        $query->{$after}();
                    }

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

    private static function getApieClass(string $class) : string
    {
        return config('apie.models_path').str_singular(studly_case($class));
    }

}
