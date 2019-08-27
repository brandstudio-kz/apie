<?php

namespace BrandStudio\Apie;

use Illuminate\Database\Eloquent\Model as OriginalModel;
use BrandStudio\Apie\Traits\ApieModelTrait;

abstract class Model extends OriginalModel
{
    use ApieModelTrait;

    abstract public static function getApieRelations() : array;
    abstract public static function getApieLevels() : array;


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

}
