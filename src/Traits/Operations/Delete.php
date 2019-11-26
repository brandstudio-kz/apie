<?php

namespace BrandStudio\Apie\Traits\Operations;

use BrandStudio\Apie\Traits\Operations\Get;

trait Delete
{

    public static function applyDelete(&$query, array $data)
    {
        static::applyFilters($query, $data);
        return $query->delete();
    }

}
