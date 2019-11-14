<?php

namespace BrandStudio\Apie\Traits\Operations;

trait Insert
{

    public static function applyInsert($class, array $data)
    {
        return $class::create($data);
    }

}
