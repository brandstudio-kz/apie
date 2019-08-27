<?php

namespace BrandStudio\Apie;

use Illuminate\Database\Eloquent\Model as OriginalModel;

class Model extends OriginalModel
{

    const SMALL = 's';
    const MEDIUM = 'm';
    const LARGE = 'l';

    private $level_attributes = [];
    private $level_relations = [];

    public static function apieQuery()
    {
        return static::query();
    }

    public function scopeLevel($query, string $levels, array $attributes = [])
    {
        $attributes = array_merge($attributes, $this->getLevelAttributeNames($levels[0]));
        $relations = $this->getLevelRelationsQuery($levels);
        $query->select($attributes)->with($relations);
    }


    private function getLevelAttributes(string $level) : array
    {
        if (isset($this->level_attributes[$level])) return $this->level_attributes;
        $this->level_attributes[$level] = array_filter(
            static::LEVELS[$level],
            function($item) {
                return !isset($item['model']);
            }
        );
        return $this->level_attributes[$level];
    }

    private function getLevelAttributeNames(string $level) : array
    {
        return array_merge(
            ['id'],
            array_keys($this->getLevelAttributes($level)),
            array_column(
                array_filter(
                    $this->getLevelRelations($level),
                    function($item) {
                        return isset($item['key']);
                    }
                ),
                'key'
            )
        );
    }



    private function getLevelRelations(string $level) : array
    {
        if (isset($this->level_relations[$level])) return $this->level_relations[$level];
        $this->level_relations[$level] = array_filter(
            static::LEVELS[$level],
            function($item) {
                return isset($item['model']);
            }
        );
        return $this->level_relations[$level];
    }

    private function getLevelRelationsQuery(string $levels) : array
    {
        $relations = $this->getLevelRelations($levels[0]);
        $relations_query = [];
        $index = 1;

        foreach($relations as $relation => $options) {
            $relations_query[$relation] = function($q) use ($levels, $index, $options) {
                $q->level($levels[$index++] ?? (strlen($levels)==1000 ? $levels : 's'), $options['child_keys'] ?? []);
            };
        }
        return $relations_query;
    }



}
